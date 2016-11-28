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
                /*$forceSave = false;

                $sOrigin = new acCouchdbJsonNative(new stdClass());

                if($s) {
                    $sOrigin = new acCouchdbJsonNative($s->toJson());
                }

                $compte = CompteClient::getInstance()->find("COMPTE-".str_replace("COMPTE-", "", $line[self::CSV_ID_COMPTE]), acCouchdbClient::HYDRATE_DOCUMENT, true);*/

                if(!$s) {
                  	$s = new Societe();
                    $s->identifiant = $identifiant;
                    $s->constructId();
                    /*if($compte) {
                        $s->compte_societe = $compte->_id;
                        $s->addCompte($compte, -1);
                        $compte->addOrigine($s->_id);
                    }*/
                }


                /*if($line[self::CSV_ID_COMPTE] && !$compte) {
                    $compte = $s->createCompteSociete(str_replace("COMPTE-", "", $line[self::CSV_ID_COMPTE]));
                    $compte->constructId();
                }*/

                /*if($s && $compte && $compte->id_societe != $s->_id) {
                    echo "Warning Le compte $compte->_id de la société  $s->_id est relié à une autre société " . $compte->id_societe ."\n";
                    $compte->id_societe = $s->_id;
                    if($compte->hasOrigine($compte->id_societe)) {
                        $compte->removeOrigine($compte->id_societe);
                        $compte->addOrigine($s->_id);
                    }
                }*/

                /*$s->setCompteSocieteObject($compte);*/

                /*$compte = ($compte) ? $compte : $s->getMasterCompte();
                if($compte && $compte->id_societe != $s->_id) {
                    $forceSave = true;
                    echo "Warning Le compte $compte->_id de la société  $s->_id est relié à une autre société " . $compte->id_societe ."\n";
                    if($compte->hasOrigine($compte->id_societe)) {
                        $compte->removeOrigine($compte->id_societe);
                        $compte->addOrigine($s->_id);
                    }
                    $oldSociete = SocieteClient::getInstance()->find($compte->id_societe);
                    if($oldSociete) {
                        $oldSociete->removeContact($compte->id_societe);
                        $oldSociete->save();
                    }
                    $compte->id_societe = $s->_id;
                    $s->compte_societe = $compte->_id;
                    $s->addCompte($compte, -1);
                    if(!$s->isNew()) {
                        $compte->save();
                    }
                }*/
                //$s->compte_societe = null;
                $s->type_societe = $line[self::CSV_FAMILLE];
                $s->raison_sociale = trim($line[self::CSV_NOM]);
        	    $s->raison_sociale_abregee = trim($line[self::CSV_NOM_COURT]);
              	$s->interpro = 'INTERPRO-declaration';
                $s->siret = str_replace(" ", "", $line[self::CSV_SIRET]);
                $s->code_naf = $line[self::CSV_CODE_NAF] ? str_replace(" ", "", $line[self::CSV_CODE_NAF]) : null;
                $s->no_tva_intracommunautaire = $line[self::CSV_TVA_INTRACOMMUNAUTAIRE] ? str_replace(" ", "", $line[self::CSV_TVA_INTRACOMMUNAUTAIRE]) : null;
                $s->commentaire = ($line[self::CSV_COMMENTAIRE]) ? $line[self::CSV_COMMENTAIRE] : null;
                $s->code_comptable_client = ($line[self::CSV_CODE_COMPTABLE_CLIENT]) ? $line[self::CSV_CODE_COMPTABLE_CLIENT] : null;
                $s->code_comptable_fournisseur = ($line[self::CSV_CODE_COMPTABLE_FOURNISSEUR]) ? $line[self::CSV_CODE_COMPTABLE_FOURNISSEUR] : null;;
                $s->statut = $line[self::CSV_STATUT];
                $this->storeCompteInfos($s, $line);

                $sFinal = new acCouchdbJsonNative($s->toJson());
                $diff = $sFinal->diff($sOrigin);
                $nouveau = $s->isNew();

                if(!count($diff)) {
                    continue;
                }

                /*if(!$compte->isNew() && !$s->isNew()) {
                    $compte->save();
                }*/

              	//$s->save();

                $modifications = null;
                foreach($diff as $key => $value) { $modifications .= "$key: $value ";}
                if($nouveau) { $modifications = "Création"; }

                echo $s->_id." (".trim($modifications).")\n";

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
