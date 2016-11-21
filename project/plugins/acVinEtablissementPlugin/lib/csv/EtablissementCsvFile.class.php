<?php

class EtablissementCsvFile extends CompteCsvFile
{
    const CSV_TYPE = 0;
    const CSV_ID_SOCIETE = 1;
    const CSV_ID = 2;
    const CSV_ID_COMPTE = 3;
    const CSV_FAMILLE = 4;
    const CSV_STATUT = 5;
    const CSV_NUM_REPRISE = 6;
    const CSV_INTITULE = 7;
    const CSV_NOM = 8;
    const CSV_NOM_COURT = 9;
    const CSV_CVI = 10;
    const CSV_NUM_INTERNE = 11;
    const CSV_SIRET = 12;
    const CSV_NO_ACCISES = 13;
    const CSV_CARTE_PRO = 14;
    const CSV_RECETTE_LOCALE = 15;
    const CSV_NATURE_INAO = 16;
    const CSV_REGION = 17;
    const CSV_ADRESSE = 18;
    const CSV_ADRESSE_COMPLEMENTAIRE_1 = 19;
    const CSV_ADRESSE_COMPLEMENTAIRE_2 = 20;
    const CSV_CODE_POSTAL = 21;
    const CSV_COMMUNE = 22;
    const CSV_INSEE = 23;
    const CSV_CEDEX = 24;
    const CSV_PAYS = 25;
    const CSV_INSEE_DECLARATION = 26;
    const CSV_COMMUNE_DECLARATION = 27;
    const CSV_TEL_BUREAU = 28;
    const CSV_TEL_PERSO = 29;
    const CSV_MOBILE = 30;
    const CSV_FAX = 31;
    const CSV_EMAIL = 32;
    const CSV_WEB = 33;
    const CSV_COMMENTAIRE = 34;
    const CSV_EXPLOITANT_ID = 35;
    const CSV_EXPLOITANT_INTITULE = 36;
    const CSV_EXPLOITANT_NOM = 37;
    const CSV_EXPLOITANT_ADRESSE = 38;
    const CSV_EXPLOITANT_CODE_POSTAL = 39;
    const CSV_EXPLOITANT_COMMUNE = 40;
    const CSV_EXPLOITANT_PAYS = 41;
    const CSV_EXPLOITANT_TEL = 42;

    private function verifyCsvLine($line) {
        if (!preg_match('/[0-9]+/', $line[self::CSV_ID_SOCIETE])) {

            throw new Exception(sprintf('ID invalide : %s', $line[self::CSV_ID_SOCIETE]));
        }
    }

    public function importEtablissements() {
        $this->errors = array();
        $etablissements = array();
        $csvs = $this->getCsv();
        foreach ($csvs as $line) {
            try {
    	        $this->verifyCsvLine($line);

                $id_societe = $line[self::CSV_ID_SOCIETE];

                $s = SocieteClient::getInstance()->find(str_replace("SOCIETE-", "", $line[self::CSV_ID_SOCIETE]));

                if (!$s) {
                  echo "WARNING: Societe ".$id_societe." n'existe pas\n";
                  continue;
                }

                $identifiant = str_replace("ETABLISSEMENT-", "", $line[self::CSV_ID]);

                $e = EtablissementClient::getInstance()->find("ETABLISSEMENT-".$identifiant);

                $eOrigin = new stdClass();
                if($e) {
                    $eOrigin = new acCouchdbJsonNative($e->toJson());
                }

                if(!$e && !$line[self::CSV_ID]) {
                    $e = EtablissementClient::getInstance()->createEtablissementFromSociete($s, $line[self::CSV_FAMILLE]);
                    $e->constructId();
                }

                if(!$e && $line[self::CSV_ID]) {
                    $e->setIdSociete($s->_id);
                    $e->setIdentifiant($identifiant);
                    $e->setFamille($line[self::CSV_FAMILLE]);
                    $e->constructId();
                }

                if(!$e->compte && $line[self::CSV_ID_COMPTE]) {
                    $compte = CompteClient::getInstance()->createCompteFromEtablissement($e);
                    $compte->addOrigine($e->_id);
                    $compte->setIdentifiant(str_replace("COMPTE-", "", $line[self::CSV_ID_COMPTE]));
                    $compte->constructId();
                    $e->setCompte($compte->_id);
                }

                $e->famille = $line[self::CSV_FAMILLE];
                if(!array_key_exists($e->famille, EtablissementFamilles::getFamilles())) {
                    printf("Warning : %s La famille %s n'est pas connue\n", $e->_id, $e->famille);
                }
              	$e->intitule = $line[self::CSV_INTITULE];
              	$e->nom = $line[self::CSV_NOM];
                $e->cvi = (isset($line[self::CSV_CVI])) ? str_replace(" ", "", $line[self::CSV_CVI]) : null;
                if($e->cvi && !preg_match("/^[0-9]+$/", $e->cvi)) {
                  $e->addCommentaire("CVI provenant de l'import : ".$e->cvi);
                  $e->cvi = null;
                }
                $e->num_interne = (isset($line[self::CSV_NUM_INTERNE])) ? str_replace(" ", "", $line[self::CSV_NUM_INTERNE]) : null;
                $e->no_accises = (isset($line[self::CSV_NO_ACCISES])) ? str_replace(" ", "", $line[self::CSV_NO_ACCISES]) : null;
                $e->carte_pro = (isset($line[self::CSV_CARTE_PRO])) ? str_replace(" ", "", $line[self::CSV_CARTE_PRO]) : null;
                $e->interpro = 'INTERPRO-declaration';
                $e->statut = ($s->statut == SocieteClient::STATUT_SUSPENDU) ? $s->statut : $line[self::CSV_STATUT];
                //$e->region = (isset($line[self::CSV_REGION])) ? $line[self::CSV_REGION] : null;

                $e->nature_inao = null;
                $natures_inao = array_flip(EtablissementClient::$natures_inao_libelles);
                if($line[self::CSV_NATURE_INAO] && !array_key_exists($line[self::CSV_NATURE_INAO], $natures_inao)) {
                    printf("Warning : %s la nature inao \"%s\" n'a pas été trouvé dans la liste #%s\n", $line[self::CSV_NATURE_INAO], implode(";", $line));
                } elseif($line[self::CSV_NATURE_INAO]){
                    $e->nature_inao = $natures_inao[$line[self::CSV_NATURE_INAO]];
                }

                $email = $e->getEmail();
                $this->storeCompteInfos($e, $line);
                if(!$e->isNew() && $e->getMasterCompte()->isInscrit()) {
                    $e->email = $email;
                }

                $e->add('declaration_insee', ($line[self::CSV_INSEE_DECLARATION]) ? $line[self::CSV_INSEE_DECLARATION] : $e->getInsee());
                $e->add('declaration_commune', ($line[self::CSV_INSEE_DECLARATION]) ? $line[self::CSV_COMMUNE_DECLARATION] : $e->getCommune());

                $eFinal = new acCouchdbJsonNative($e->toJson());
                $diff = $eFinal->diff($eOrigin);

                if(!count($diff)) {
                    continue;
                }

                if(isset($compte) && $compte->isNew()) {
                    exit;
                    //$compte->save();
                }

                //$e->save();

                $modifications = null;
                foreach($diff as $key => $value) {
                    $modifications .= "$key: $value ";
                }

                echo $e->_id." (".trim($modifications).")\n";
            } catch(Exception $e) {
                echo $e->getMessage()."\n";
            }
        }

        return $etablissements;
    }

    protected function getField($line, $strConstant) {

        eval("\$constante = self::".$strConstant.";" );

        return $line[$constante];
    }

    public static function export($etablissement) {

        $exploitant = $etablissement->getCompteExploitantObject();

        return array(
            "ETABLISSEMENT",
            $etablissement->id_societe,
            $etablissement->identifiant,
            $etablissement->famille,
            $etablissement->statut,
            null,
            $etablissement->intitule,
            $etablissement->nom,
            $etablissement->cvi,
            $etablissement->num_interne,
            $etablissement->siret,
            $etablissement->no_accises,
            $etablissement->carte_pro,
            $etablissement->adresse,
            $etablissement->code_postal,
            $etablissement->commune,
            $etablissement->insee,
            $etablissement->pays,
            $etablissement->declaration_insee,
            $etablissement->declaration_commune,
            $etablissement->telephone_bureau,
            $etablissement->telephone_perso,
            $etablissement->fax,
            $etablissement->email,
            $exploitant->civilite,
            $exploitant->nom,
            $exploitant->adresse,
            $exploitant->code_postal,
            $exploitant->commune,
            $exploitant->pays,
            $exploitant->telephone_perso,
        );
    }

}
