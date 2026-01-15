<?php

class VracCsvImport extends CsvFile
{
    const CSV_CONTRAT = 0;
    const CSV_CAMPAGNE = 1;
    const CSV_TYPE_CONTRAT = 2;
    const CSV_DUREE_CONTRAT_PLURI = 3;
    const CSV_NUMERO_INTERNE = 4;
    const CSV_NUMERO_CONTRAT_CADRE = 5;

    const CSV_TYPE_TRANSACTION = 6; // Raisin / Bouteille / Vrac

    const CSV_ACHETEUR_CVI = 7;
    const CSV_ACHETEUR_NOM = 8;
    const CSV_ACHETEUR_TVA = 9;
    const CSV_VENDEUR_CVI = 10;
    const CSV_VENDEUR_NOM = 11;
    const CSV_VENDEUR_TVA = 12;
    const CSV_COURTIER_MANDATAIRE_SIRET = 13;
    const CSV_COURTIER_MANDATAIRE_NOM = 14;

    const CSV_VIN_CERTIFICATION = 15;
    const CSV_VIN_GENRE = 16;
    const CSV_VIN_APPELLATION = 17;
    const CSV_VIN_MENTION = 18;
    const CSV_VIN_LIEU = 19;
    const CSV_VIN_COULEUR = 20;
    const CSV_VIN_CEPAGE = 21;
    const CSV_VIN_CODE_INAO = 22;
    const CSV_VIN_LIBELLE = 23;
    const CSV_VIN_LABEL = 24; // HVE / BIO
    const CSV_VIN_VTSGN = 25;
    const CSV_VIN_DENOMINATION = 26;
    const CSV_VIN_MILLESIME = 27;

    const CSV_QUANTITE = 28;
    const CSV_QUANTITE_TYPE = 29;

    const CSV_PRIX_UNITAIRE = 30;
    const CSV_PRIX_UNITE = 31;

    const CSV_CLAUSE_VENDEUR_FRAIS_ANNEXES = 32;
    const CSV_CLAUSE_ACHETEUR_PRIMES_DIVERSES = 33;
    const CSV_CLAUSE_RESERVE_PROPRIETE = 34;
    const CSV_CLAUSE_DELAI_PAIEMENT = 35;
    const CSV_CLAUSE_RESILIATION = 36;
    const CSV_CLAUSE_MANDAT_FACTURATION = 37;
    const CSV_CLAUSE_CRITERE_EVOLUTION_PRIX = 38;
    const CSV_CLAUSE_CRITERE_RENEGOCIATION_PRIX = 39;
    const CSV_CLAUSE_SUIVI_QUALITATIF = 40;
    const CSV_CLAUSE_DELAI_RETIRAISON = 41;
    const CSV_CLAUSE_AUTRES = 42;

    // Import initial
    const CSV_CREATEUR = 43;
    const CSV_DATE_SAISIE = 44;
    const CSV_DATE_SIGNATURE_VENDEUR = 45;
    const CSV_DATE_SIGNATURE_ACHETEUR = 46;
    const CSV_DATE_SIGNATURE_COURTIER_MANDATAIRE = 47;
    const CSV_DATE_VALIDATION = 48;
    const CSV_DATE_CLOTURE = 49;

    const LABEL_BIO = 'agriculture_biologique';

    public static $labels_array = [self::LABEL_BIO => "Agriculture Biologique"];

    public static $headers = [
        "CONTRAT", "Campagne", "Type de contrat", "Durée en année", "Numero contrat", "Numéro du contrat cadre référent",
        "Type de vente", "Acheteur CVI", "Acheteur nom", "Acheteur TVA", "Vendeur CVI", "Vendeur nom", "Vendeur TVA", "Courtier siret",
        "Courtier nom", "Certification", "Genre", "Appellation", "Mention", "Lieu", "Couleur", "Cépage", "Code INAO",
        "Libelle produit", "Label", "VT/SGN", "Dénomination", "Millesime", "Quantité", "Quantité type", "Prix unitaire",
        "Prix unite", "Frais annexes vendeur", "Primes diverses acheteur", "Clause réserve propriété", "Clause délai paiement",
        "Clause résiliation", "Mandat facturation", "Critères et modalités d’évolution des prix", "Critères de renégociation du prix",
        "Suivi qualitatif", "Délai maximum de retiraison", "Autres clauses particulières", "Créateur", "Date de saisie",
        "Date de signature vendeur", "Date de signature acheteur", "Date de signature courtier", "Date de validation", "Date de cloture", "Numero de visa", "Statut", "Centilisation", "Quantité enlevé", "Date retiraison", "Id du document"
    ];

    /** @var array<string> $imported ID des vracs importés */
    protected static $imported = [];

    /** @var int $line Numero de ligne du CSV */
    protected static $line = 0;

    /** @var array $errors Tableau des erreurs de vérification */
    private $errors = [];

    /** @var array $warnings Tableau des warnings de la vérification */
    private $warnings = [];

    /** @var Configuration $configuration La configuration produit
    private $configuration = null;

    /** @var array $found_etablissements Cache des établissements */
    private static $found_etablissements = [];

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
     * Ajoute une erreur dans le tableau
     *
     * @param int $ligne La ligne de l'erreur
     * @param string $code Le code d'erreur
     * @param string $raison Description
     */
    public function addError($ligne, $code, $raison)
    {
        $e = new stdClass();
        $e->num_ligne = $ligne;
        $e->erreur_csv = $code;
        $e->raison = $raison;
        $this->errors[] = $e;
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
     * Vérification que le fichier CSV contient uniquement le type de contrat
     * spécifié à l'import
     *
     * @param string $type_contrat Le type de contrat (application / cadre)
     */
    public function hasMixedContratType($type_contrat)
    {
        $wrong = array_filter(array_column($this->getCsv(), self::CSV_TYPE_CONTRAT), function ($type) use ($type_contrat) {
            return $type !== $type_contrat;
        });

        if (count($wrong)) {
            $this->addError(0, "mixed_contrat_type", count($wrong) . " contrat(s) sont différents du type spécifié ($type_contrat)");
        }
    }

    /**
     * Extrait les numéros de contrats internes du CSV et vérifie qu'il n'existe
     * pas déjà dans la base. Émet une erreur le cas échéant
     *
     * @param string $identifiant l'identifiant du créateur
     */
    public function hasExistingVrac($identifiant)
    {
        $ids = $this->getContratsImportables();
        $etab = $this->guessId($identifiant);

        foreach (VracTousView::getInstance()->findBy($etab->_id) as $existingVrac) {
            if (isset($existingVrac->value->numero_papier) && in_array($existingVrac->value->numero_papier, $ids)) {
                $this->addError(0, "contrat_existant", "Le contrat avec le numéro interne ".$existingVrac->value->numero_papier." existe déjà");
            }
        }
    }

    public function preimportChecks(CSVVRAC $csvVrac)
    {
        $compteIdentifiant = CompteClient::getInstance()->find($csvVrac->identifiant)->getEtablissementInformations()->getCvi();

        $this->hasExistingVrac($compteIdentifiant);
        $this->hasMixedContratType($csvVrac->type_contrat);
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
        $current = null;
        $v = null;
        $produitPosition = 0;

        foreach ($this->getCsv() as $line) {
            self::$line++;

            if ($current !== $line[self::CSV_NUMERO_INTERNE]) {
                try {
                    $createur = $this->guessId($line[self::CSV_ACHETEUR_CVI]);
                } catch (Exception $e) {
                    $this->addError(self::$line, "operateur_inexistant", "L'identifiant du créateur n'a pas été reconnu [".$line[self::CSV_ACHETEUR_CVI]."] (".$e->getMessage().")");
                    continue;
                }
                if ($verified && $v) {
                    $v->updateTotaux();
                    if($v->isApplicationPluriannuel()) {
                        $v->createApplication($createur);
                    }
                    $v->save();
                    self::$imported[] = $v->_id;
                }
                if($line[self::CSV_TYPE_CONTRAT] == VracClient::TEMPORALITE_PLURIANNUEL_APPLICATION) {
                    $vCadre = VracClient::getInstance()->findByNumeroContrat($line[self::CSV_NUMERO_CONTRAT_CADRE]);
                    $v = $vCadre->generateNextPluriannuelApplication();
                    $v->remove('declaration');
                    $v->add('declaration');
                } else {
                    $v = VracClient::getInstance()->createVrac(
                        $createur->_id,
                        $line[self::CSV_DATE_SAISIE]
                    );
                    $v->campagne = $line[self::CSV_CAMPAGNE];
                    $v->numero_papier = $line[self::CSV_NUMERO_INTERNE];
                    $v->type_contrat = $line[self::CSV_TYPE_TRANSACTION];

                    try {
                        $acheteur = $this->guessId($line[self::CSV_ACHETEUR_CVI]);
                    } catch (Exception $e) {
                        $this->addError(self::$line, "operateur_inexistant", "L'identifiant de l'acheteur n'a pas été reconnu [".$line[self::CSV_ACHETEUR_CVI]."] (".$e->getMessage().")");
                        continue;
                    }
                    $v->acheteur_identifiant = $acheteur->_id;
                    $v->acheteur_assujetti_tva = $this->guessBool('Acheteur assujetti tva', $line[self::CSV_ACHETEUR_TVA]);
                    $v->storeAcheteurInformations($acheteur);

                    try {
                        $vendeur = $this->guessId($line[self::CSV_VENDEUR_CVI]);
                    } catch (Exception $e) {
                        $this->addError(self::$line, "operateur_inexistant", "L'identifiant du vendeur n'a pas été reconnu [".$line[self::CSV_VENDEUR_CVI]."] (".$e->getMessage().")");
                        continue;
                    }
                    $v->vendeur_identifiant = $vendeur->_id;
                    $v->vendeur_assujetti_tva = $this->guessBool('Vendeur assujetti tva', $line[self::CSV_VENDEUR_TVA]);
                    $v->storeVendeurInformations($vendeur);

                    if ($line[self::CSV_COURTIER_MANDATAIRE_SIRET]) {
                        try {
                            $mandataire = $this->guessId($line[self::CSV_COURTIER_MANDATAIRE_SIRET]);
                        } catch (Exception $e) {
                            $this->addError(self::$line, "operateur_inexistant", "L'identifiant du mandataire n'a pas été reconnu [".$line[self::CSV_COURTIER_MANDATAIRE_SIRET]."] (".$e->getMessage().")");
                            continue;
                        }
                        $v->mandataire_identifiant = $mandataire->_id;
                        $v->storeMandataireInformations($mandataire);
                    }
                }
                $current = $line[self::CSV_NUMERO_INTERNE];
                $produitPosition = 0;
            }

            if ($v === null) {
                throw new sfException('Le vrac devrait être initialisé...');
            }

            // Section produit
            $produit = $this->guessProduit($line, $v);

            if (! $produit) {
                $this->addError(self::$line, "produit_non_reconnu", "Produit non reconnu [".$line[self::CSV_VIN_LIBELLE]."]");
                continue;
            }

            $produit->actif = 1;
            $produit->position = $produitPosition++;

            $produit->millesime = $line[self::CSV_VIN_MILLESIME];

            if ($line[self::CSV_TYPE_CONTRAT] === VracClient::TEMPORALITE_PLURIANNUEL_CADRE) {
                $produit->millesime = null;
            }

            $produit->getOrAdd('label');
            if ($line[self::CSV_VIN_LABEL]) {
                $produit->label = $line[self::CSV_VIN_LABEL];
            } else {
                $produit->label = "AUCUNE";
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

            $v->prix_unite = isset(VracClient::$prix_unites[$line[self::CSV_PRIX_UNITE]]) ? VracClient::$prix_unites[$line[self::CSV_PRIX_UNITE]] : $line[self::CSV_PRIX_UNITE];

            $v->contrat_pluriannuel = ($line[self::CSV_TYPE_CONTRAT] === VracClient::TEMPORALITE_PLURIANNUEL_APPLICATION) ? 1 : 0;
            /* if ($v->contrat_pluriannuel) {
                $v->add('reference_contrat_pluriannuel', $line[self::CSV_NUMERO_CONTRAT_CADRE]);
            } */

            $v->add('clause_reserve_propriete', $this->guessBool('Clause réserve propriété', $line[self::CSV_CLAUSE_RESERVE_PROPRIETE]));
            $v->add('clause_mandat_facturation', $this->guessBool('Clause mandat facturation', $line[self::CSV_CLAUSE_MANDAT_FACTURATION]));
            $v->add('conditions_paiement', $line[self::CSV_CLAUSE_DELAI_PAIEMENT]);
            $v->add('clause_resiliation', $line[self::CSV_CLAUSE_RESILIATION]);
            $v->add('vendeur_frais_annexes', $line[self::CSV_CLAUSE_VENDEUR_FRAIS_ANNEXES]);
            $v->add('acheteur_primes_diverses', $line[self::CSV_CLAUSE_ACHETEUR_PRIMES_DIVERSES]);
            $v->add('clause_renegociation_prix', $this->guessBool('Clause renégociation du prix', $line[self::CSV_CLAUSE_CRITERE_RENEGOCIATION_PRIX]));

            if ($line[self::CSV_TYPE_CONTRAT] === VracClient::TEMPORALITE_PLURIANNUEL_CADRE) {
                $v->add('duree_annee', $line[self::CSV_DUREE_CONTRAT_PLURI]);
                $v->add('clause_evolution_prix', $line[self::CSV_CLAUSE_CRITERE_EVOLUTION_PRIX]);
            }

            if ($v->type_contrat === VracClient::TYPE_VRAC) {
                $v->add('suivi_qualitatif', $this->guessBool('Suivi qualitatif', $line[self::CSV_CLAUSE_SUIVI_QUALITATIF]));
            }

            // $v->valide->date_saisie = $line[self::CSV_DATE_SAISIE];
            // $v->valide->date_validation_vendeur = $line[self::CSV_DATE_SIGNATURE_VENDEUR];
            // $v->valide->date_validation_acheteur = $line[self::CSV_DATE_SIGNATURE_ACHETEUR];
            // $v->valide->date_validation_mandataire = isset($line[self::CSV_DATE_SIGNATURE_COURTIER_MANDATAIRE]) ? $line[self::CSV_DATE_SIGNATURE_COURTIER_MANDATAIRE] : null;
            // $v->valide->date_validation = $line[self::CSV_DATE_VALIDATION];
            // $v->valide->date_cloture = $line[self::CSV_DATE_CLOTURE];
            $v->etape = "validation";

            if (!$verified) {
                $validator = new VracContratValidation($v);

                if ($validator->hasErreurs()) {
                    foreach ($validator->getErreurs() as $err) {
                        $this->addError(self::$line, $err->getCode(), $err->getMessage().': '.$err->getInfo());
                    }
                }

                if ($validator->hasVigilances()) {
                    foreach ($validator->getVigilances() as $warn) {
                        $this->warnings[self::$line][] = $warn->getMessage() . ': ' .  $warn->getInfo();
                    }
                }
            }
        }

        if ($verified && $v) {
            $v->updateTotaux();
            if($v->isApplicationPluriannuel()) {
                $v->createApplication($createur);
            }
            $v->save();
            self::$imported[] = $v->_id;
        }

        return array_values(array_unique(self::$imported));
    }

    /**
     * Inscrit les erreurs dans l'objet CSVVRAC
     *
     * @param csvVrac le CSVVrac où inscrire les erreurs
     */
    public function checkErreurs(CSVVRAC $csvVrac)
    {
        if (count($this->getErrors())) {
            $csvVrac->documents = [];
            $csvVrac->statut = CSVVRACClient::LEVEL_ERROR;
            foreach ($this->getErrors() as $error) {
                $csvVrac->addErreur($error);
            }
        }
    }

    /**
     * Retourne le nombre de contrat importable du fichier
     * Se base sur le nombre unique de CSV_NUMERO_INTERNE
     *
     * @return int
     */
    public function getContratsImportables()
    {
        return array_values(array_unique(array_column($this->getCsv(), self::CSV_NUMERO_INTERNE)));
    }

    /**
     * Formatte le CSV au format tableau pour le récap
     *
     * @return array
     */
    public function display()
    {
        // numéros de contrats
        $contrats = $this->getContratsImportables();
        $ret = [];

        foreach ($contrats as $numero_interne) {
            $ret[$numero_interne] = [
                'soussignes' => [
                    'acheteur' => null,
                    'vendeur' => null,
                    'courtier' => null
                ],
                'produits' => []
            ];

            $courtier = $vendeur = $acheteur = null;

            $filtered = array_filter($this->getCsv(), function ($v) use ($numero_interne) {
                return $numero_interne === $v[self::CSV_NUMERO_INTERNE];
            });

            foreach ($filtered as $entry) {
                $acheteur = $acheteur ?: $this->guessId($entry[self::CSV_ACHETEUR_CVI]);
                $vendeur = $vendeur ?: $this->guessId($entry[self::CSV_VENDEUR_CVI]);
                if ($entry[self::CSV_COURTIER_MANDATAIRE_SIRET]) {
                    $courtier = $courtier ?: $this->guessId($entry[self::CSV_COURTIER_MANDATAIRE_SIRET]);
                }

                $v = new Vrac(); // "obligatoire" pour récupérer l'objet produit via addProduit
                $v->campagne = $entry[self::CSV_CAMPAGNE]; // Sans la campagne, la récupération de la conf plante
                $ret[$numero_interne]['type_contrat'] = $entry[self::CSV_TYPE_TRANSACTION];
                $ret[$numero_interne]['temporalite_contrat'] = $entry[self::CSV_TYPE_CONTRAT];
                $produit = $this->guessProduit($entry, $v);

                $ret[$numero_interne]['soussignes']['acheteur'] = $acheteur;
                $ret[$numero_interne]['soussignes']['vendeur'] = $vendeur;
                $ret[$numero_interne]['soussignes']['courtier'] = $courtier;

                $ret[$numero_interne]['produits'][] = [
                    'libelle' => $produit->getLibelleComplet(),
                    'millesime' => $entry[self::CSV_VIN_MILLESIME],
                    'volume' => $entry[self::CSV_QUANTITE] . ' ' . strtolower($entry[self::CSV_QUANTITE_TYPE]),
                    'prix' => $entry[self::CSV_PRIX_UNITAIRE] . " " . ( isset(VracClient::$prix_unites[$entry[self::CSV_PRIX_UNITE]]) ? VracClient::$prix_unites[$entry[self::CSV_PRIX_UNITE]] : $entry[self::CSV_PRIX_UNITE]),
                ];

                unset($v);
            }
        }

        return $ret;
    }

    /**
     * Ajoute une annexe à chaque Vrac du tableau $imported
     *
     * @param $annexe L'annexe à ajouter
     * @param $name Le nom de l'annexe à ajouter
     */
    public function addAnnexe($annexe = null, $name = 'Annexe_contrat')
    {
        if (! $annexe) {
            return;
        }

        $temp = $annexe;

        if (! is_file($temp)) {
            $temp = tempnam(sys_get_temp_dir(), 'ANX_');
            file_put_contents($temp, file_get_contents($annexe));
        }

        foreach (self::$imported as $vid) {
            $vrac = VracClient::getInstance()->find($vid);

            if ($vrac) {
                $vrac->storeAttachment($temp, mime_content_type($temp), $name);
                $vrac->save();
            }
        }

        if (is_file($temp)) {
            unlink($temp);
        }
    }

    /**
     * Retourne 0 ou 1 en fonction de la valeur donnée
     *
     * @param $key string
     * @param $value string
     * @return null|0|1
     */
    public function guessBool($key, $value)
    {
        if (in_array($value, [1, "1", "OUI", true], true) === true) {
            return 1;
        } elseif (in_array($value, [0, "0", "NON", false], true) === true) {
            return 0;
        } else {
            $this->addError(self::$line, "invalid_value", "La valeur saisie [$value] du champs $key n'est pas reconnue");
            return null;
        }
    }

    /**
     * Retourne le produit défini dans la ligne du csv
     * Identification par code inao si présent, sinon par libellé
     *
     * @param array $line Une ligne du CSV
     * @param Vrac $v Un objet Vrac
     * @return null|VracDetail
     */
    private function guessProduit(array $line, Vrac $v)
    {
        $this->configuration = isset($this->configuration) ? $this->configuration : ConfigurationClient::getInstance()->getCurrent();
        $produitConfig = null;

        if ($line[self::CSV_VIN_CODE_INAO]) {
            $produitConfig = $this->configuration->identifyProductByCodeDouane($line[self::CSV_VIN_CODE_INAO]);
            $produitConfig = current($produitConfig);
        }

        if (! $produitConfig) {
            $produitConfig = $this->configuration->identifyProductByLibelle($line[self::CSV_VIN_LIBELLE]);
        }

        if (! $produitConfig) {
            return null;
        }

        $hash_produit = HashMapper::inverse($produitConfig->getHash(), "VRAC");
        $produit = $v->addProduit($hash_produit)->addDetail($hash_produit);

        return $produit;
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
        if (array_key_exists($numero, self::$found_etablissements)) {
            return self::$found_etablissements[$numero];
        }

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

        self::$found_etablissements[$numero] = $res;

        return $res;
    }
}
