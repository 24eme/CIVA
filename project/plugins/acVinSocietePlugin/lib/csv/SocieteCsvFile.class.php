<?php

class SocieteCsvFile extends CompteCsvFile
{
    const CSV_TYPE = 0;
    // const CSV_ID_SOCIETE = 1;
    const CSV_ID = 2;
    const CSV_ID_COMPTE = 3;
    const CSV_FAMILLE = 4;
    const CSV_STATUT = 5;
    const CSV_INTITULE = 6;
    const CSV_NOM = 7;
    const CSV_NOM_COURT = 8;
    const CSV_CODE_COMPTABLE_CLIENT = 9;
    const CSV_CODE_COMPTABLE_FOURNISSEUR = 10;
    const CSV_SIRET = 11;
    const CSV_TVA_INTRACOMMUNAUTAIRE = 12;
    const CSV_CODE_NAF = 13;
    // const CSV_RECETTE_LOCALE = 14;
    // const CSV_NATURE_INAO = 15;
    // const CSV_REGION = 16;
    const CSV_ADRESSE = 17;
    const CSV_ADRESSE_COMPLEMENTAIRE_1 = 18;
    const CSV_ADRESSE_COMPLEMENTAIRE_2 = 19;
    const CSV_CODE_POSTAL = 20;
    const CSV_COMMUNE = 21;
    const CSV_INSEE = 22;
    const CSV_CEDEX = 23;
    const CSV_PAYS = 24;
    // const CSV_INSEE_DECLARATION = 25;
    // const CSV_COMMUNE_DECLARATION = 26;
    const CSV_TEL_BUREAU = 27;
    const CSV_TEL_PERSO = 28;
    const CSV_MOBILE = 29;
    const CSV_FAX = 30;
    const CSV_EMAIL = 31;
    const CSV_WEB = 32;
    const CSV_COMMENTAIRE = 33;
    const CSV_SOCIETE_LIEE = 34;
    // const CSV_EXPLOITANT_INTITULE = 34;
    // const CSV_EXPLOITANT_NOM = 35;
    // const CSV_EXPLOITANT_ADRESSE = 36;
    // const CSV_EXPLOITANT_CODE_POSTAL = 37;
    // const CSV_EXPLOITANT_COMMUNE = 38;
    // const CSV_EXPLOITANT_PAYS = 39;
    // const CSV_EXPLOITANT_TEL = 40;
    // const CSV_EXPLOITANT_DATE_NAISSANCE = 41;

    public function importSocietes() {
        $this->errors = array();
        $societes = array();
        $csvs = $this->getCsv();
        foreach ($csvs as $line) {
            if($line[self::CSV_TYPE] != "SOCIETE") {
                continue;
            }

            try {
                $identifiant = str_replace("SOCIETE-", "", $line[self::CSV_ID]);
                $sOrigin = new acCouchdbJsonNative(new stdClass());
                $s = SocieteClient::getInstance()->find("SOCIETE-".$identifiant);
                if($s) {
                    $sOrigin = new acCouchdbJsonNative($s->toJson());
                }

                if(!$s) {
                  	$s = new Societe();
                    $s->identifiant = $identifiant;
                    $s->constructId();
                }

                $s->compte_societe = "COMPTE-".$s->identifiant;
                $s->type_societe = $line[self::CSV_FAMILLE];
                $s->raison_sociale = trim($line[self::CSV_NOM]);
        	    $s->raison_sociale_abregee = (trim($line[self::CSV_NOM_COURT])) ? trim($line[self::CSV_NOM_COURT]) : null;
              	$s->interpro = 'INTERPRO-declaration';
                $s->siret = str_replace(" ", "", $line[self::CSV_SIRET]);
                $s->code_naf = $line[self::CSV_CODE_NAF] ? str_replace(" ", "", $line[self::CSV_CODE_NAF]) : null;
                $s->no_tva_intracommunautaire = $line[self::CSV_TVA_INTRACOMMUNAUTAIRE] ? str_replace(" ", "", $line[self::CSV_TVA_INTRACOMMUNAUTAIRE]) : null;
                $s->commentaire = ($line[self::CSV_COMMENTAIRE]) ? $line[self::CSV_COMMENTAIRE] : null;
                $s->code_comptable_client = ($line[self::CSV_CODE_COMPTABLE_CLIENT]) ? $line[self::CSV_CODE_COMPTABLE_CLIENT] : null;
                $s->code_comptable_fournisseur = ($line[self::CSV_CODE_COMPTABLE_FOURNISSEUR]) ? $line[self::CSV_CODE_COMPTABLE_FOURNISSEUR] : null;;
                $s->statut = $line[self::CSV_STATUT];
                $this->storeCompteInfos($s, $line);

                $s->cleanEtablissements();
                $s->cleanComptes();

                $sFinal = new acCouchdbJsonNative($s->toJson());
                $diffFinal = $sFinal->diff($sOrigin);
                $diffOrigin = $sOrigin->diff($sFinal);
                $nouveau = $s->isNew();

                if(!count($diffFinal) && !count($diffOrigin)) {
                    continue;
                }

              	$s->save();



                $modifications = null;
                foreach($diffFinal as $key => $value) { $modifications .= "$key: $value ";}
                foreach($diffOrigin as $key => $value) { $modifications .= "$key: -$value ";}
                if($nouveau) { $modifications = "Création"; }

                echo $s->_id." (".trim($modifications).")\n";

                if(isset($line[self::CSV_SOCIETE_LIEE])) {
                    foreach(explode("|", $line[self::CSV_SOCIETE_LIEE]) as $societeLieeId) {
                        if(!$societeLieeId) {
                            continue;
                        }
                        $s->addAndSaveSocieteLiee($societeLieeId);
                    }
                }

            }catch(Exception $e) {
                echo $e->getMessage()." ".$line[self::CSV_ID]."\n";
            }
        }

        return $societes;
    }

    protected function getField($line, $strConstant) {

        eval("\$constante = self::".$strConstant.";" );

        return $line[$constante];
    }

}
