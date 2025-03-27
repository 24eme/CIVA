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

    const CSV_HASH_CERTIFICATION = 8;
    const CSV_HASH_GENRE = 9;
    const CSV_HASH_APPELLATION = 10;
    const CSV_HASH_MENTION = 11; // VT / SGN
    const CSV_HASH_LIEU = 12;
    const CSV_HASH_COULEUR = 13;
    const CSV_HASH_CEPAGE = 13;

    const CSV_VIN_CODE_INAO = 15;
    const CSV_VIN_LIBELLE = 16;
    const CSV_VIN_MENTION = 17; // HVE / BIO
    const CSV_VIN_VTSGN = 18;
    const CSV_VIN_DENOMINATION = 19;
    const CSV_VIN_MILLESIME = 20;
    const CSV_VIN_CEPAGE = 21;

    const CSV_QUANTITE = 22;
    const CSV_QUANTITE_TYPE = 23;

    const CSV_PRIX_UNITAIRE = 24;
    const CSV_PRIX_UNITE = 25;

    const CSV_PLURIANNUEL = 26;
    const CSV_PLURIANNUEL_CONTRAT_CADRE = 27;

    const CSV_CLAUSE_RESERVE_PROPRIETE = 28;
    const CSV_CLAUSE_DELAI_PAIEMENT = 29;
    const CSV_CLAUSE_RESILIATION = 30;
    const CSV_CLAUSE_MANDAT_FACTURATION = 31;

    const CSV_DATE_SIGNATURE_VENDEUR = 32;
    const CSV_DATE_SIGNATURE_ACHETEUR = 33;
    const CSV_DATE_SIGNATURE_COURTIER_MANDATAIRE = 34;
    const CSV_DATE_SAISIE = 35;
    const CSV_DATE_VALIDATION = 36;
    const CSV_DATE_CLOTURE = 37;

    const LABEL_BIO = 'agriculture_biologique';

    public static $labels_array = [self::LABEL_BIO => "Agriculture Biologique"];

    /** @var int $imported Nombre de vrac importé */
    protected static $imported = 0;

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
     * Générateur qui renvoie les lignes du CSV une à une
     *
     * @yield array $line Un vrac
     */
    public function getLines() {
        foreach ($this->csvdata as $line) {
            yield $line;
        }
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

    /**
     * Importe des vracs dans la base
     * Si `$verified` est égal à `false`, alors rien n'est importé, mais
     * les erreurs / warnings sont générés (mode dry-run)
     *
     * @param bool $verified Le csv a été vérifié
     * @return int Nombre de vracs importés
     */
    public function import($verified = false) {
        /** @var Configuration $configuration */
        $configuration = ConfigurationClient::getInstance()->getCurrent();
        $current = null;
        $v = null;

        foreach ($this->getLines() as $line) {
            if ($current !== $line[self::CSV_NUMERO_INTERNE]) {
                $v = VracClient::getInstance()->createVrac(
                    $line[self::CSV_CREATEUR_IDENTIFIANT],
                    $line[self::CSV_DATE_SAISIE]
                );
                $v->campagne = $line[self::CSV_CAMPAGNE];
                $current = $line[self::CSV_NUMERO_INTERNE];
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
            // Identification par code inao si présent, sinon par hash, et enfin par libellé en dernier recours
            $produitConfig = null;

            if ($line[self::CSV_VIN_CODE_INAO]) {
                $produitConfig = $configuration->identifyProductByCodeDouane($line[self::CSV_VIN_CODE_INAO]);
            }

            if (! $produitConfig && $line[self::CSV_HASH_CERTIFICATION]) {
                $produitConfig = $configuration->identifyProduct(
                    $line[self::CSV_HASH_CERTIFICATION],
                    $line[self::CSV_HASH_GENRE],
                    $line[self::CSV_HASH_APPELLATION],
                    $line[self::CSV_HASH_MENTION],
                    $line[self::CSV_HASH_LIEU],
                    $line[self::CSV_HASH_COULEUR],
                    $line[self::CSV_HASH_CEPAGE]
                );
            }

            if (! $produitConfig) {
                $produitConfig = $configuration->identifyProductByLibelle($line[self::CSV_VIN_LIBELLE]);
            }

            if (! $produitConfig) {
                $this->errors[] = [
                    "line" => self::$line,
                    "context" => "Contrat: ".$line[self::CSV_NUMERO_INTERNE],
                    "message" => "Produit non reconnu [".$line[self::CSV_VIN_LIBELLE]."]",
                ];
                continue;
            }

            $hash_produit = HashMapper::inverse($produitConfig->getHash(), "VRAC");
            $produit = $v->addProduit($hash_produit)->addDetail($hash_produit);

            $produit->millesime = $line[self::CSV_VIN_MILLESIME];

            if ($line[self::CSV_VIN_MENTION]) {
                $produit->getOrAdd('label');
                $produit->label = $line[self::CSV_VIN_MENTION];
            }

            if ($line[self::CSV_VIN_DENOMINATION]) {
                $produit->denomination = $line[self::CSV_VIN_DENOMINATION];
            }

            if ($v->type_contrat === VracClient::TYPE_RAISIN) {
                $produit->surface_propose = $line[self::CSV_QUANTITE];
            }

            $produit->vtsgn = $line[self::CSV_VIN_VTSGN] ?? null;
            $produit->prix_unitaire = $line[self::CSV_PRIX_UNITAIRE];
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

            $v->valide->date_saisie = $line[self::CSV_DATE_SAISIE];
            $v->valide->date_validation_vendeur = $line[self::CSV_DATE_SIGNATURE_VENDEUR];
            $v->valide->date_validation_acheteur = $line[self::CSV_DATE_SIGNATURE_ACHETEUR];
            $v->valide->date_validation_mandataire = isset($line[self::CSV_DATE_SIGNATURE_COURTIER_MANDATAIRE]) ? $line[self::CSV_DATE_SIGNATURE_COURTIER_MANDATAIRE] : null;
            $v->valide->date_validation = $line[self::CSV_DATE_VALIDATION];
            $v->valide->date_cloture = $line[self::CSV_DATE_CLOTURE];
            $v->valide->status = Vrac::STATUT_CLOTURE;

            if ($verified) {
                $v->valide->statut = $line[self::CSV_STATUT];
                $v->updateTotaux();
                $v->save();

                self::$imported++;
            } else {
                $validator = new VracValidation($v);

                if ($validator->hasErreurs()) {
                    foreach ($validator->getErreurs() as $err) {
                        $this->errors[self::$line][] = $err->getMessage() . ': ' . $err->getInfo();
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

        return self::$imported;
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

        $res = EtablissementClient::getInstance()->find($res[0]->id);
        if (! $res instanceof Etablissement) {
            throw new Exception($numero . " transformation en établissement échouée");
        }

        return $res;
    }
}
