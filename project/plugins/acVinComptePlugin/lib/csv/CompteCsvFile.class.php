<?php

class CompteCsvFile extends CsvFile
{
    const CSV_TYPE = 0;
    const CSV_ID_MASTER = 1;
    const CSV_ID = 2;
    const CSV_ID_COMPTE = 3;
    //const CSV_FAMILLE = 4;
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

    public function importComptes() {
        $this->errors = array();
        $csvs = $this->getCsv();
        foreach ($csvs as $line) {
            if($line[self::CSV_TYPE] != "COMPTE") {
                continue;
            }

            try{
                $e = EtablissementClient::getInstance()->find(sprintf("ETABLISSEMENT-%s", str_replace("ETABLISSEMENT-", "", $line[self::CSV_ID_MASTER])));

                if(!$e) {
                    throw new sfException(sprintf("Document introuvable '%s'", $line[self::CSV_ID_MASTER]));
                }

                $s = $e->getSociete();
                $identifiant = str_replace("COMPTE-", "", $line[self::CSV_ID]);

                $cOrigin = new acCouchdbJsonNative(new stdClass());
                $c = CompteClient::getInstance()->find("COMPTE-".str_replace("COMPTE-", "", $line[self::CSV_ID]));

                if($c) {
                    $cOrigin = new acCouchdbJsonNative($c->toJson());
                }

                if(!$c) {
                    $c = new Compte();
                    $c->id_societe = $s->_id;
                    $c->identifiant = $identifiant;
                    $c->interpro = 'INTERPRO-declaration';
                    $c->constructId();
                }

                $c->remove('origines');
                $c->add('origines');

                $c->addOrigine($e->_id);

                if($c->id_societe != $s->_id) {
                    echo "Warning le compte $c->_id a changé de société : $c->id_societe => $s->_id\n";
                    $c->changeSociete($s->_id);
                    $c->remove('droits');
                }

                $c->statut = ($line[self::CSV_STATUT]) ? $line[self::CSV_STATUT] : $s->statut;

                if($c->statut == CompteClient::STATUT_ACTIF && !$c->mot_de_passe) {
                    $c->mot_de_passe = "{TEXT}" . sprintf("%04d", rand(1000, 9999));
                }

                $c->civilite = $e->getIntitule();
                $c->nom = $e->getNom();
                $c->updateNomAAfficher();
                $email = $c->email;
                $e->pushContactAndAdresseTo($c);
                if($c->isInscrit()) {
                    $c->email = $email;
                }

                $updateDroits = false;
                if($c->isActif() && (!$c->exist('droits') || !count($c->_get('droits')->toArray(true, false)))) {
                    $c->add('droits', $c->getDroits());
                    $updateDroits = true;
                }

                $cFinal = new acCouchdbJsonNative($c->toJson());
                $diffFinal = $cFinal->diff($cOrigin);
                $diffOrigin = $cOrigin->diff($cFinal);
                $nouveau = $c->isNew();

                if(!count($diffFinal) && !count($diffOrigin)) {
                    continue;
                }

                $modifications = null;
                foreach($diffFinal as $key => $value) { $modifications .= "$key: $value ";}
                foreach($diffOrigin as $key => $value) { $modifications .= " $key: -$value ";}
                if($nouveau) { $modifications = "Création"; }
                if($updateDroits) { $modifications .= " (mise à jour des droits)"; }

                $c->save();

                echo $c->_id." (".trim($modifications).")\n";
        	} catch(Exception $e) {
               echo $e->getMessage()."\n";
            }
        }
    }

    protected function storeCompteInfos(InterfaceCompteGenerique $c, $line) {
        $c->setAdresseComplementaire(null);
        $c->adresse = trim($this->getField($line, 'CSV_ADRESSE'));
        if(preg_match('/[a-z]/i', $this->getField($line, 'CSV_ADRESSE_COMPLEMENTAIRE_1'))) {
           $c->setAdresseComplementaire(trim(preg_replace('/,/', '', $this->getField($line, 'CSV_ADRESSE_COMPLEMENTAIRE_1'))));
            if(preg_match('/[a-z]/i', $this->getField($line, 'CSV_ADRESSE_COMPLEMENTAIRE_2'))) {
                $c->setAdresseComplementaire($c->getAdresseComplementaire(). " ; ".trim(preg_replace('/,/', '', $this->getField($line, 'CSV_ADRESSE_COMPLEMENTAIRE_2'))));
            }
        }

        if($this->getField($line, 'CSV_CEDEX')) {
            $c->adresse_complementaire .= (($c->adresse_complementaire) ?  " ; " : null).$this->getField($line, 'CSV_CEDEX');
        }

        $c->code_postal = trim($this->getField($line, 'CSV_CODE_POSTAL'));
        $c->commune = $this->getField($line, 'CSV_COMMUNE');
        $c->insee = $this->getField($line, 'CSV_INSEE') ? $this->getField($line, 'CSV_INSEE') : null;
        $c->pays = $this->getField($line, 'CSV_PAYS');

        if(preg_match("/^FRANCE$/i", $this->getField($line, 'CSV_PAYS'))) {
            $c->pays = 'FR';
        }

        if(!$c->pays) {
            $pays = ConfigurationClient::getInstance()->findCountry($this->getField($line, 'CSV_PAYS'));
            $c->pays = ($pays) ? $pays : null;
        }

        $c->email = $this->formatAndVerifyEmail($this->getField($line, 'CSV_EMAIL'), $c);
        $c->fax = $this->formatAndVerifyPhone($this->getField($line, 'CSV_FAX'), $c);
        $c->telephone_perso = $this->formatAndVerifyPhone($this->getField($line, 'CSV_TEL_PERSO'), $c);
        $c->telephone_bureau = $this->formatAndVerifyPhone($this->getField($line, 'CSV_TEL_BUREAU'), $c);
        $c->telephone_mobile = $this->formatAndVerifyPhone($this->getField($line, 'CSV_MOBILE'), $c);
        $c->site_internet = null;
        if($this->getField($line, 'CSV_WEB')) {
            if (preg_match('/^http:\/\/[^ ]+$/', $this->getField($line, 'CSV_WEB'))) {
                $c->site_internet = $this->getField($line, 'CSV_WEB');
            }else{
                if (preg_match('/www.[^ ]+$/', $this->getField($line, 'CSV_WEB'))) {
                    $c->site_internet = 'http://'.$this->getField($line, 'CSV_WEB');
                }else{
                    echo("WARNING: ".$c->identifiant.": site non valide : \"".$this->getField($line, 'CSV_WEB')."\"\n");
                    $c->addCommentaire("Problème d'import, site non valide : \"".$this->getField($line, 'CSV_WEB')."\"");
                }
            }
        }
    }

    protected function getField($line, $strConstant) {

        eval("\$constante = self::".$strConstant.";" );

        return $line[$constante];
    }

    protected function formatAndVerifyPhone($phone, $c) {

        $phone = str_replace("+33", "0", trim($phone));
        $phone = preg_replace("/[\._ -]/", "", $phone);

        if($phone && strlen($phone) == 9) {
            $phone = "0".$phone;
        }

        if($phone && !preg_match("/^[0-9]{10}$/", $phone) && !preg_match("/^00/", $phone)) {
            printf("WARNING: ".$c->_id.": Problème d'import : Le numéro de téléphone n'est pas correct %s\n", $phone);
            //$c->addCommentaire(sprintf("Problème d'import : Le numéro de téléphone n'est pas correct %s", $phone));
            //return null;
        }

        return ($phone) ? $phone : null;
    }

    protected function formatAndVerifyEmail($email, $c) {
        $email = trim($email);

        if($email && !preg_match("/^[a-z0-9çéèàâê_\.-]+@[a-z0-9\.-]+$/i", $email)) {
            printf("WARNING: ".$c->_id.": L'email n'est pas correct %s\n", $email);
            //$c->addCommentaire(sprintf("Problème d'import: L'email n'est pas correct %s", $email));
            //return null;
        }

        return ($email) ? $email : null;
    }

}
