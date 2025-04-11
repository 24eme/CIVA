<?php

class VracCsvImport extends CsvFile
{
    const CSV_CONTRAT = 0;
    const CSV_CAMPAGNE = 1;
    const CSV_NUMERO_INTERNE = 2;

    const CSV_CREATEUR_IDENTIFIANT = 3;
    const CSV_ACHETEUR_CVI = 4;
    const CSV_VENDEUR_CVI = 5;
    const CSV_COURTIER_MANDATAIRE_SIRET = 6;

    const CSV_TYPE_TRANSACTION = 7; // Raisin / Bouteille / Vrac

    const CSV_VIN_CODE_INAO = 8;
    const CSV_VIN_LIBELLE = 9;
    const CSV_VIN_MENTION = 10; // HVE / BIO
    const CSV_VIN_VTSGN = 11;
    const CSV_VIN_DENOMINATION = 12;
    const CSV_VIN_CEPAGE = 13;
    const CSV_VIN_MILLESIME = 14;

    const CSV_QUANTITE = 15;
    const CSV_QUANTITE_TYPE = 16;

    const CSV_PRIX_UNITAIRE = 17;
    const CSV_PRIX_UNITE = 18;

    const CSV_PLURIANNUEL = 19;
    const CSV_PLURIANNUEL_CONTRAT_CADRE = 20;

    const CSV_CLAUSE_RESERVE_PROPRIETE = 21;
    const CSV_CLAUSE_DELAI_PAIEMENT = 22;
    const CSV_CLAUSE_RESILIATION = 23;
    const CSV_CLAUSE_MANDAT_FACTURATION = 24;
    const CSV_CLAUSE_VENDEUR_FRAIS_ANNEXES = 25;
    const CSV_CLAUSE_ACHETEUR_PRIMES_DIVERSES = 26;

    const CSV_DATE_SIGNATURE_VENDEUR = 27;
    const CSV_DATE_SIGNATURE_ACHETEUR = 28;
    const CSV_DATE_SIGNATURE_COURTIER_MANDATAIRE = 29;
    const CSV_DATE_SAISIE = 30;
    const CSV_DATE_VALIDATION = 31;
    const CSV_DATE_CLOTURE = 32;

    const LABEL_BIO = 'agriculture_biologique';

    public static $labels_array = [self::LABEL_BIO => "Agriculture Biologique"];

    public static $headers = [
        "CONTRAT", "Campagne", "Numero contrat", "Createur CVI", "Acheteur CVI", "Vendeur CVI", "Courtier siret",
        "Type de vente", "Code INAO", "Libelle produit", "Label", "VT/SGN", "Denomination", "Cepage", "Millesime",
        "Quantite", "Quantite type", "Prix unitaire", "Prix unite", "Pluriannuel", "Contrat cadre",
        "Clause reserve propriété", "Clause délai paiement", "Clause résiliation", "Clause mandat facturation",
        "Vendeur frais annexes", "Acheteur primes diverses", "Date de signature vendeur", "Date de signature acheteur",
        "Date de signature courtier", "Date de saisie", "Date de validation", "Date de cloture"
    ];

    /** @var array<string> $imported ID des vracs importés */
    protected static $imported = [];

    /** @var int $line Numero de ligne du CSV */
    protected static $line = 1;

    /** @var array $errors Tableau des erreurs de vérification */
    private $errors = [];

    /** @var array $warnings Tableau des warnings de la vérification */
    private $warnings = [];

    /**
     * Crée une instance depuis un tableau CSV
     *
     * @param array $lines Le CSV transformé en tableau
     * @param bool $headers Le csv contient une ligne de header ?
     * @return self
     */
    public static function createFromArray(array $lines, $headers = true) {
        if ($headers) {
            array_shift($lines);
            self::$line++;
        }

        $class = new self();
        $class->csvdata = $lines;

        return $class;
    }

    /**
     * Retourne le tableau contenant les erreurs
     *
     * @return array Le tableau d'erreur
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Retourne le tableau contenant les avertissements
     *
     * @return array Le tableau des avertissements
     */
    public function getWarnings() {
        return $this->warnings;
    }

    public static function getHeaders()
    {
        return self::$headers;
    }

    /**
     * Importe des vracs dans la base
     * Si `$verified` est égal à `false`, alors rien n'est importé, mais
     * les erreurs / warnings sont générés (mode dry-run)
     *
     * @param bool $verified Le csv a été vérifié
     * @return array<string> Tableau d'ID des vracs importés
     */
    public function import($verified = false) {
        /** @var Configuration $configuration */
        $configuration = ConfigurationClient::getInstance()->getCurrent();
        $current = null;
        $v = null;
        $produitPosition = 0;

        foreach ($this->getCsv() as $line) {
            if ($current !== $line[self::CSV_NUMERO_INTERNE]) {
                $createur = $this->guessId($line[self::CSV_CREATEUR_IDENTIFIANT]);

                $v = VracClient::getInstance()->createVrac(
                    $createur->_id,
                    $line[self::CSV_DATE_SAISIE]
                );
                $v->campagne = $line[self::CSV_CAMPAGNE];
                $v->numero_papier = $line[self::CSV_NUMERO_INTERNE];
                $current = $line[self::CSV_NUMERO_INTERNE];

                $produitPosition = 0;
            }

            if ($v === null) {
                throw new sfException('Le vrac devrait être initialisé...');
            }

            $v->type_contrat = $line[self::CSV_TYPE_TRANSACTION];

            $acheteur = $this->guessId($line[self::CSV_ACHETEUR_CVI]);
            $v->acheteur_identifiant = $acheteur->_id;
            $v->storeAcheteurInformations($acheteur);

            $vendeur = $this->guessId($line[self::CSV_VENDEUR_CVI]);
            $v->vendeur_identifiant = $vendeur->_id;
            $v->storeVendeurInformations($vendeur);

            if ($line[self::CSV_COURTIER_MANDATAIRE_SIRET]) {
                $mandataire = $this->guessId($line[self::CSV_COURTIER_MANDATAIRE_SIRET]);
                $v->mandataire_identifiant = $mandataire->_id;
                $v->storeMandataireInformations($mandataire);
            }

            // Section produit
            // Identification par code inao si présent, sinon par libellé
            $produitConfig = null;

            if ($line[self::CSV_VIN_CODE_INAO]) {
                $produitConfig = $configuration->identifyProductByCodeDouane($line[self::CSV_VIN_CODE_INAO]);
                $produitConfig = current($produitConfig);
            }

            if (! $produitConfig) {
                $produitConfig = $configuration->identifyProductByLibelle($line[self::CSV_VIN_LIBELLE]);
            }

            if (! $produitConfig) {
                $e = new stdClass();
                $e->num_ligne = self::$line;
                $e->erreur_csv = "produit_non_reconnu";
                $e->raison = "Produit non reconnu [".$line[self::CSV_VIN_LIBELLE]."]";
                $this->errors[] = $e;
                continue;
            }

            $hash_produit = HashMapper::inverse($produitConfig->getHash(), "VRAC");
            $produit = $v->addProduit($hash_produit)->addDetail($hash_produit);
            $produit->actif = 1;
            $produit->position = $produitPosition++;

            $produit->millesime = $line[self::CSV_VIN_MILLESIME];

            if ($line[self::CSV_VIN_MENTION]) {
                $produit->getOrAdd('label');
                $produit->label = $line[self::CSV_VIN_MENTION];
            }

            if ($line[self::CSV_VIN_DENOMINATION]) {
                $produit->denomination = $line[self::CSV_VIN_DENOMINATION];
            }

            if ($v->type_contrat === VracClient::TYPE_RAISIN) {
                $produit->surface_propose = (float) $line[self::CSV_QUANTITE];
            }

            $produit->vtsgn = $line[self::CSV_VIN_VTSGN] ?? null;
            $produit->prix_unitaire = (float) $line[self::CSV_PRIX_UNITAIRE];
            // Fin produit

            $v->prix_unite = $line[self::CSV_PRIX_UNITE];

            $v->contrat_pluriannuel = $line[self::CSV_PLURIANNUEL] === "APPLICATION" ? 1 : 0;
            if ($v->contrat_pluriannuel) {
                $v->reference_contrat_pluriannuel = $line[self::CSV_PLURIANNUEL_CONTRAT_CADRE];
            }

            $v->add('clause_reserve_propriete', $line[self::CSV_CLAUSE_RESERVE_PROPRIETE] === "OUI" ? 1 : 0);
            $v->add('clause_mandat_facturation', $line[self::CSV_CLAUSE_MANDAT_FACTURATION] === "OUI" ? 1 : 0);
            $v->add('conditions_paiement', $line[self::CSV_CLAUSE_DELAI_PAIEMENT]);
            $v->add('clause_resiliation', $line[self::CSV_CLAUSE_RESILIATION]);
            $v->add('vendeur_frais_annexes', $line[self::CSV_CLAUSE_VENDEUR_FRAIS_ANNEXES]);
            $v->add('acheteur_primes_diverses', $line[self::CSV_CLAUSE_ACHETEUR_PRIMES_DIVERSES]);

            $v->valide->date_saisie = $line[self::CSV_DATE_SAISIE];
            $v->valide->date_validation_vendeur = $line[self::CSV_DATE_SIGNATURE_VENDEUR];
            $v->valide->date_validation_acheteur = $line[self::CSV_DATE_SIGNATURE_ACHETEUR];
            $v->valide->date_validation_mandataire = isset($line[self::CSV_DATE_SIGNATURE_COURTIER_MANDATAIRE]) ? $line[self::CSV_DATE_SIGNATURE_COURTIER_MANDATAIRE] : null;
            $v->valide->date_validation = $line[self::CSV_DATE_VALIDATION];
            $v->valide->date_cloture = $line[self::CSV_DATE_CLOTURE];
            $v->valide->statut = Vrac::STATUT_CLOTURE;

            if ($verified) {
                $v->updateTotaux();
                $v->save();

                self::$imported[] = $v->_id;
            } else {
                $validator = new VracContratValidation($v);

                if ($validator->hasErreurs()) {
                    foreach ($validator->getErreurs() as $err) {
                        $e = new stdClass();
                        $e->num_ligne = self::$line;
                        $e->erreur_csv = $err->getCode();
                        $e->raison = $err->getMessage() . ': ' . $err->getInfo();
                        $this->errors[] = $e;
                    }
                }

                if ($validator->hasVigilances()) {
                    foreach ($validator->getVigilances() as $warn) {
                        $this->warnings[self::$line][] = $warn->getMessage() . ': ' .  $warn->getInfo();
                    }
                }
            }

            self::$line++;
        }

        return array_unique(self::$imported);
    }

    /**
     * Trouve le numero d'identifiant en fonction d'un autre
     *
     * @param string $numero Le numéro d'accise, de siret, ou de cvi
     * @throw Exception Si identifiant inconnu
     * @return Etablissement L'établissement correspondant à l'identifiant
     */
    private function guessId($numero)
    {
        $res = EtablissementAllView::getInstance()->findByEtablissement($numero);

        if ($res === null) {
            $res = EtablissementClient::getInstance()->findByCvi($numero);
        }

        if ($res === null) {
            throw new Exception($numero . " ne correspond à aucun établissement");
        }

        if (is_array($res)) {
            $res = EtablissementClient::getInstance()->find($res[0]->id);
        } else {
            $res = EtablissementClient::getInstance()->find($res->_id);
        }

        if (! $res instanceof Etablissement) {
            throw new Exception($numero . " transformation en établissement échouée");
        }

        return $res;
    }
}
