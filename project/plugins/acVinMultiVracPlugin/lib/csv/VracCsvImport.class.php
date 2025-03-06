<?php

class VracCsvImport extends CsvFile
{
    const CSV_CONTRAT = 0;
    const CSV_CAMPAGNE = 1;
    const CSV_STATUT = 2;
    const CSV_NUMERO_CONTRAT = 3;
    const CSV_NUMERO_ARCHIVE = 4;

    const CSV_CREATEUR_NUMERO = 5;
    const CSV_ACHETEUR_NUMERO = 6;
    const CSV_ACHETEUR_NOM = 7;
    const CSV_VENDEUR_NUMERO = 8;
    const CSV_VENDEUR_NOM = 9;
    const CSV_COURTIER_MANDATAIRE_NUMERO = 10;
    const CSV_COURTIER_MANDATAIRE_NOM = 11;

    const CSV_TYPE_TRANSACTION = 12;

    const CSV_VIN_LIBELLE = 13;
    const CSV_VIN_MENTION = 14; // HVE / BIO
    const CSV_VIN_VTSGN = 15;
    const CSV_VIN_DOMAINE = 16;
    const CSV_MILLESIME = 17;

    const CSV_CEPAGE = 18;
    const CSV_VOLUME_PROPOSE = 19;
    const CSV_VOLUME_ENLEVE = 20;
    const CSV_PRIX_UNITAIRE = 21;
    const CSV_DATE_ENLEVEMENT = 22;

    const CSV_PLURIANNUEL = 23;
    const CSV_PLURIANNUEL_CAMPAGNES = 24;

    const CSV_RESERVE_PROPRIETE = 25;
    const CSV_DATE_SIGNATURE = 26;

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
     * @param array $array Le CSV transformé en tableau
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
     *
     * @param bool $verified Le csv a été vérifier
     * @return int Nombre de vracs importés
     */
    public function import($verified = false) {
        $configuration = ConfigurationClient::getInstance()->getCurrent();
        $current = null;

        foreach ($this->getLines() as $line) {
            if ($current !== $line[self::CSV_NUMERO_CONTRAT]) {
                $v = new Vrac();
                $v->initVrac(
                    $configuration,
                    $line[self::CSV_CREATEUR_NUMERO],
                    $line[self::CSV_NUMERO_CONTRAT],
                    $line[self::CSV_DATE_SIGNATURE],
                    $line[self::CSV_CAMPAGNE]
                );
                $current = $line[self::CSV_NUMERO_CONTRAT];
            }

            $v->type_contrat = $line[self::CSV_TYPE_TRANSACTION];

            $acheteur = $this->guessId($line[self::CSV_ACHETEUR_NUMERO]);
            if ($acheteur !== false) {
                $v->acheteur_identifiant = $acheteur->_id;
                $v->storeAcheteurInformations($acheteur);
            }

            $vendeur = $this->guessId($line[self::CSV_VENDEUR_NUMERO]);
            if ($vendeur !== false) {
                $v->vendeur_identifiant = $vendeur->_id;
                $v->storeVendeurInformations($vendeur);
            }

            if ($line[self::CSV_COURTIER_MANDATAIRE_NUMERO]) {
                $mandataire = $this->guessId($line[self::CSV_COURTIER_MANDATAIRE_NUMERO]);
                if ($mandataire !== false) {
                    $v->mandataire_identifiant = $mandataire->_id;
                    $v->storeMandataireInformations($mandataire);
                }
            }

            // Section produit
            $produitConfig = $configuration->identifyProductByLibelle($line[self::CSV_VIN_LIBELLE]);
            if (! $produitConfig) {
                echo "ERR: Produit non reconnu [".$line[self::CSV_VIN_LIBELLE]."]".PHP_EOL;
                continue;
            }

            $hash_produit = HashMapper::inverse($produitConfig->getHash(), "VRAC");
            $produit = $v->addProduit($hash_produit)->addDetail($hash_produit);

            $produit->millesime = $line[self::CSV_MILLESIME];

            if ($line[self::CSV_VIN_MENTION]) {
                $produit->getOrAdd('label');
                $produit->label = $line[self::CSV_VIN_MENTION];
            }

            if ($line[self::CSV_VIN_DOMAINE]) {
                $produit->denomination = $line[self::CSV_VIN_DOMAINE];
            }

            $produit->vtsgn = $line[self::CSV_VIN_VTSGN] ?? null;

            if ($v->type_contrat === VracClient::TYPE_VRAC) {
                $produit->volume_propose = $line[self::CSV_VOLUME_PROPOSE];
                $produit->volume_enleve = $line[self::CSV_VOLUME_ENLEVE];

                if ($line[self::CSV_DATE_ENLEVEMENT]) {
                    $produit->retiraisons->add(null, ['date' => $line[self::CSV_DATE_ENLEVEMENT], 'volume' => $produit->volume_enleve]);
                }
            }

            $produit->prix_unitaire = $line[self::CSV_PRIX_UNITAIRE];

            // Fin produit
            $v->contrat_pluriannuel = $line[self::CSV_PLURIANNUEL] === "PLURIANNUEL" ? 1 : 0;
            if ($v->contrat_pluriannuel) {
                $v->reference_contrat_pluriannuel = substr($v->numero_contrat, -1, 4);
            }

            $v->add('clause_reserve_propriete', $line[self::CSV_RESERVE_PROPRIETE]);

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
     * @throw Exception
     * @return Etablissement L'identifiant à trouver ou false
     */
    private function guessId($numero)
    {
        $res = EtablissementAllView::getInstance()->findByEtablissement($numero);

        if ($res === null) {
            $res = EtablissementClient::getInstance()->findByCvi($numero);
        }

        if ($res === null) {
            throw new Exception($numero . " ne correspond à aucun établissement");
            $res = false;
        }

        $res = EtablissementClient::getInstance()->find($res[0]->id);
        if (! $res instanceof Etablissement) {
            throw new Exception($numero . " transformation en établissement échouée");
        }

        return $res;
    }
}
