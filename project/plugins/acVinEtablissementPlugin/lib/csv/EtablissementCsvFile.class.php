<?php

class EtablissementCsvFile extends CompteCsvFile
{
    const CSV_TYPE = 0;
    const CSV_ID_SOCIETE = 1;
    const CSV_ID = 2;
    const CSV_ID_COMPTE = 3;
    const CSV_FAMILLE = 4;
    const CSV_STATUT = 5;
    const CSV_INTITULE = 6;
    const CSV_NOM = 7;
    // const CSV_NOM_COURT = 8;
    const CSV_CVI = 9;
    const CSV_NUM_INTERNE = 10;
    const CSV_SIRET = 11;
    const CSV_NO_ACCISES = 12;
    const CSV_CARTE_PRO = 13;
    const CSV_RECETTE_LOCALE = 14;
    const CSV_NATURE_INAO = 15;
    const CSV_REGION = 16;
    const CSV_ADRESSE = 17;
    const CSV_ADRESSE_COMPLEMENTAIRE_1 = 18;
    const CSV_ADRESSE_COMPLEMENTAIRE_2 = 19;
    const CSV_CODE_POSTAL = 20;
    const CSV_COMMUNE = 21;
    const CSV_INSEE = 22;
    const CSV_CEDEX = 23;
    const CSV_PAYS = 24;
    const CSV_INSEE_DECLARATION = 25;
    const CSV_COMMUNE_DECLARATION = 26;
    const CSV_TEL_BUREAU = 27;
    const CSV_TEL_PERSO = 28;
    const CSV_MOBILE = 29;
    const CSV_FAX = 30;
    const CSV_EMAIL = 31;
    const CSV_WEB = 32;
    const CSV_COMMENTAIRE = 33;
    const CSV_EXPLOITANT_INTITULE = 34;
    const CSV_EXPLOITANT_NOM = 35;
    const CSV_EXPLOITANT_ADRESSE = 36;
    const CSV_EXPLOITANT_CODE_POSTAL = 37;
    const CSV_EXPLOITANT_COMMUNE = 38;
    const CSV_EXPLOITANT_PAYS = 39;
    const CSV_EXPLOITANT_TEL = 40;
    const CSV_EXPLOITANT_DATE_NAISSANCE = 41;

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
            if($line[self::CSV_TYPE] != "ETABLISSEMENT") {
                continue;
            }

            try {
    	        $this->verifyCsvLine($line);

                $id_societe = $line[self::CSV_ID_SOCIETE];

                $s = SocieteClient::getInstance()->find(str_replace("SOCIETE-", "", $line[self::CSV_ID_SOCIETE]));

                if (!$s) {
                  echo "WARNING: Societe ".$id_societe." n'existe pas\n";
                  continue;
                }

                $identifiant = str_replace("ETABLISSEMENT-", "", $line[self::CSV_ID]);

                $eOrigin = new acCouchdbJsonNative(new stdClass());
                $e = EtablissementClient::getInstance()->find("ETABLISSEMENT-".$identifiant);

                if($e) {
                    $eOrigin = new acCouchdbJsonNative($e->toJson());
                }

                if(!$e && $line[self::CSV_ID]) {
                    $e = new Etablissement();
                    $e->setIdentifiant($identifiant);
                    $e->setIdSociete($s->_id);
                    $e->constructId();
                }

                if($e->id_societe != $s->_id) {
                    echo "Warning l'établissement $e->_id a changé de société : $e->id_societe => $s->_id\n";
                    $e->changeSociete($s->_id);
                }

                $e->compte = "COMPTE-".$identifiant;
                $e->famille = ($line[self::CSV_FAMILLE]) ? $line[self::CSV_FAMILLE] : null;

                if(!array_key_exists($e->famille, EtablissementFamilles::getFamilles())) {
                    printf("Warning : %s La famille %s n'est pas connue\n", $e->_id, $e->famille);
                }
              	$e->intitule = ($line[self::CSV_INTITULE]) ? $line[self::CSV_INTITULE] : null;
              	$e->nom = $line[self::CSV_NOM];
                $e->cvi = (isset($line[self::CSV_CVI]) && str_replace(" ", "", $line[self::CSV_CVI])) ? str_replace(" ", "", $line[self::CSV_CVI]) : null;
                if($e->cvi && !preg_match("/^[0-9]+$/", $e->cvi)) {
                    printf("Warning : %s Le CVI n'est pas correct : %s\n", $e->_id, $e->cvi);
                  //$e->addCommentaire("CVI provenant de l'import : ".$e->cvi);
                  //$e->cvi = null;
                }
                $e->num_interne = (isset($line[self::CSV_NUM_INTERNE]) && str_replace(" ", "", $line[self::CSV_NUM_INTERNE])) ? str_replace(" ", "", $line[self::CSV_NUM_INTERNE]) : null;
                $e->no_accises = (isset($line[self::CSV_NO_ACCISES]) && str_replace(" ", "", $line[self::CSV_NO_ACCISES])) ? str_replace(" ", "", $line[self::CSV_NO_ACCISES]) : null;
                $e->carte_pro = (isset($line[self::CSV_CARTE_PRO]) && str_replace(" ", "", $line[self::CSV_CARTE_PRO])) ? str_replace(" ", "", $line[self::CSV_CARTE_PRO]) : null;
                $e->interpro = 'INTERPRO-declaration';
                $e->statut = $line[self::CSV_STATUT];
                $e->region = (isset($line[self::CSV_REGION])) ? $line[self::CSV_REGION] : null;

                $e->nature_inao = null;
                $natures_inao = array_flip(EtablissementClient::$natures_inao_libelles);
                if($line[self::CSV_NATURE_INAO] && !array_key_exists($line[self::CSV_NATURE_INAO], $natures_inao)) {
                    printf("Warning : %s la nature inao \"%s\" n'a pas été trouvé dans la liste #%s\n", $line[self::CSV_NATURE_INAO], implode(";", $line));
                } elseif($line[self::CSV_NATURE_INAO]){
                    $e->nature_inao = $natures_inao[$line[self::CSV_NATURE_INAO]];
                }

                $this->storeCompteInfos($e, $line);

                $e->add('declaration_insee', ($line[self::CSV_INSEE_DECLARATION]) ? $line[self::CSV_INSEE_DECLARATION] : $e->getInsee());
                $e->add('declaration_commune', ($line[self::CSV_INSEE_DECLARATION]) ? $line[self::CSV_COMMUNE_DECLARATION] : $e->getCommune());

                $e->add('exploitant');
                $e->exploitant->setCivilite(($line[self::CSV_EXPLOITANT_INTITULE]) ? $line[self::CSV_EXPLOITANT_INTITULE] : null);
                $e->exploitant->setNom(($line[self::CSV_EXPLOITANT_NOM]) ? $line[self::CSV_EXPLOITANT_NOM] : null);
                $e->exploitant->setAdresse(($line[self::CSV_EXPLOITANT_ADRESSE]) ? $line[self::CSV_EXPLOITANT_ADRESSE] : null);
                $e->exploitant->setCodePostal(($line[self::CSV_EXPLOITANT_CODE_POSTAL]) ? $line[self::CSV_EXPLOITANT_CODE_POSTAL] : null);
                $e->exploitant->setCommune(($line[self::CSV_EXPLOITANT_COMMUNE]) ? $line[self::CSV_EXPLOITANT_COMMUNE] : null);
                $e->exploitant->setTelephone(($line[self::CSV_EXPLOITANT_TEL]) ? $line[self::CSV_EXPLOITANT_TEL] : null);
                $e->exploitant->setDateNaissance(($line[self::CSV_EXPLOITANT_DATE_NAISSANCE]) ? $line[self::CSV_EXPLOITANT_DATE_NAISSANCE] : null);

                $eFinal = new acCouchdbJsonNative($e->toJson());
                $diffFinal = $eFinal->diff($eOrigin);
                $diffOrigin = $eOrigin->diff($eFinal);
                $nouveau = $e->isNew();

                if(!count($diffFinal) && !count($diffOrigin)) {
                    continue;
                }
                $modifications = null;
                foreach($diffFinal as $key => $value) { $modifications .= "$key: $value ";}
                if($nouveau) { $modifications = "Création"; }

                $e->save();

                echo $e->_id." (".trim($modifications).")\n";
            } catch(Exception $e) {
                echo $e->getMessage()." ".$line[self::CSV_ID]."\n";
            }
        }

        return $etablissements;
    }

    protected function getField($line, $strConstant) {

        eval("\$constante = self::".$strConstant.";" );

        return $line[$constante];
    }

    public static function export($etablissement) {

        $exploitant = $etablissement->add('exploitant');

        return array(
            "ETABLISSEMENT",
            $etablissement->id_societe,
            $etablissement->identifiant,
            str_replace("COMPTE-", "", $etablissement->compte),
            $etablissement->famille,
            $etablissement->statut,
            $etablissement->intitule,
            $etablissement->nom,
            null,
            $etablissement->cvi,
            $etablissement->num_interne,
            $etablissement->siret,
            $etablissement->no_accises,
            $etablissement->carte_pro,
            null,
            null,
            $etablissement->region,
            $etablissement->adresse,
            null,
            null,
            $etablissement->code_postal,
            $etablissement->commune,
            $etablissement->insee,
            null,
            $etablissement->pays,
            $etablissement->declaration_insee,
            $etablissement->declaration_commune,
            $etablissement->telephone_bureau,
            $etablissement->telephone_perso,
            null,
            $etablissement->fax,
            $etablissement->email,
            null,
            null,
            $exploitant->civilite,
            $exploitant->nom,
            $exploitant->adresse,
            $exploitant->code_postal,
            $exploitant->commune,
            "FR",
            $exploitant->telephone,
            $exploitant->date_naissance,
        );
    }

}
