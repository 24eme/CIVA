<?php

class Db2Tiers2Csv
{
    protected $file = null;
    protected $csv = null;
    protected $_insee = null;

    public function __construct($file) {
        $this->file = $file;
        $this->csv = array();
        $this->newFillArray(false);
        $this->newFillArray(true);
        //$this->fillArray();
    }

    public function getEtablissements() {
        $etablissements = array();
        $lines = $this->getArrayCsv();
        foreach($lines as $line) {
            if($line[0] != "ETABLISSEMENT") {
                continue;
            }
            $etablissements["ETABLISSEMENT-".str_replace("ETABLISSEMENT-", "", $line[2])] = $line;
        }

        return $etablissements;
    }

    public function printCsv() {
        foreach($this->getArrayCsv() as $line) {
            echo implode(";", $line)."\n";
        }
    }

    public function getArrayCsv() {

        return $this->csv;
    }

    public function newFillArray($suspendu = false) {
        $lines = file($this->file);

        $tiersComplet = array();
        $etablissements = array();
        $societes = array();

        foreach ($lines as $a) {
            $db2Tiers = new Db2Tiers(str_getcsv($a, ",", '"'));
            if(!$db2Tiers->isEtablissement()) {
                continue;
            }

            $tiersComplet[$db2Tiers->get(Db2Tiers::COL_NUM)] = $db2Tiers;
        }

        ksort($tiersComplet, SORT_NUMERIC);

        foreach($tiersComplet as $db2Tiers) {
            if($db2Tiers->getFamille() == EtablissementFamilles::FAMILLE_PRODUCTEUR) {
                continue;
            }

            $etablissements[$db2Tiers->get(Db2Tiers::COL_NUM)][] = $db2Tiers;
            unset($tiersComplet[$db2Tiers->get(Db2Tiers::COL_NUM)]);
        }

        foreach($tiersComplet as $db2Tiers) {
            if(isset($etablissements[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)])) {
                continue;
            }
            if($db2Tiers->get(Db2Tiers::COL_NO_STOCK) != $db2Tiers->get(Db2Tiers::COL_NUM)) {
                continue;
            }
            if(!$db2Tiers->get(db2Tiers::COL_CIVABA)) {
                continue;
            }
            $etablissements[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)][] = $db2Tiers;
            unset($tiersComplet[$db2Tiers->get(Db2Tiers::COL_NUM)]);
        }

        foreach($tiersComplet as $db2Tiers) {
            $premierEtablissement = null;
            if(!isset($etablissements[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)])) {
                continue;
            }

            if(count($etablissements[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)]) != 1) {
                continue;
            }

            $premierEtablissement = $etablissements[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)][0];

            if($premierEtablissement->getFamille() != EtablissementFamilles::FAMILLE_PRODUCTEUR || !$premierEtablissement->get(DB2Tiers::COL_CIVABA)) {
                continue;
            }

            if(!$db2Tiers->get(db2Tiers::COL_CVI)) {
                continue;
            }

            $etablissements[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)][] = $db2Tiers;
            unset($tiersComplet[$db2Tiers->get(Db2Tiers::COL_NUM)]);
        }

        foreach($tiersComplet as $db2Tiers) {
            $etablissements[$db2Tiers->get(Db2Tiers::COL_NUM)][] = $db2Tiers;
            unset($tiersComplet[$db2Tiers->get(Db2Tiers::COL_NUM)]);
        }


        if(count($tiersComplet) > 0) {
            foreach($tiersComplet as $db2Tiers) {
                $db2Tiers->printDebug();
            }
            throw new Exception("Erreurs tous les tiers n'ont pas été réparti");
        }


        foreach($etablissements as $num => $tiers) {
            if(!$suspendu && $this->isCloture($tiers)) {
                unset($etablissements[$num]);
                continue;
            }

            if($suspendu && !$this->isCloture($tiers)) {
                unset($etablissements[$num]);
                continue;
            }

            if($this->isCloture($tiers) && in_array($this->getFamille($tiers), array(EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR)) && !$this->getInfos($tiers, Db2Tiers::COL_CVI)) {
                unset($etablissements[$num]);
                continue;
            }
        }

        ksort($etablissements, SORT_NUMERIC);

        foreach($etablissements as $num => $tiers) {
            foreach($tiers as $t) {
                if($t->get(Db2Tiers::COL_NO_STOCK) != $t->get(Db2Tiers::COL_NUM) || $t->get(Db2Tiers::COL_NUM) != $t->get(Db2Tiers::COL_MAISON_MERE)) {
                    break;
                }

                $societes[$t->get(Db2Tiers::COL_NO_STOCK)][$num] = $tiers;
                unset($etablissements[$num]);
                break;
            }
        }

        foreach($etablissements as $num => $tiers) {
            foreach($tiers as $t) {
                if(!isset($societes[$t->get(Db2Tiers::COL_NO_STOCK)])) {
                    continue;
                }

                if($this->existFamille($societes[$t->get(Db2Tiers::COL_NO_STOCK)], $t->getFamille())) {
                    continue;
                }

                $societes[$t->get(Db2Tiers::COL_NO_STOCK)][$num] = $tiers;
                unset($etablissements[$num]);
                break;
            }
        }

        foreach($etablissements as $num => $tiers) {
            foreach($tiers as $t) {
                if(!isset($societes[$t->get(Db2Tiers::COL_MAISON_MERE)])) {
                    break;
                }

                if($this->existFamille($societes[$t->get(Db2Tiers::COL_MAISON_MERE)], $t->getFamille())) {
                    continue;
                }

                $societes[$t->get(Db2Tiers::COL_MAISON_MERE)][$num] = $tiers;
                unset($etablissements[$num]);
                break;
            }
        }

        foreach($etablissements as $num => $tiers) {
            foreach($tiers as $t) {
                if(isset($societes[$t->get(Db2Tiers::COL_NO_STOCK)]) && $this->existFamille($societes[$t->get(Db2Tiers::COL_NO_STOCK)], $t->getFamille())) {
                    continue;
                }

                $societes[$t->get(Db2Tiers::COL_NO_STOCK)][$num] = $tiers;
                unset($etablissements[$num]);
                break;
            }
        }

        foreach($etablissements as $num => $tiers) {
            foreach($tiers as $t) {
                $societes[$num][$num] = $tiers;
                unset($etablissements[$num]);
                continue;
            }
        }

        if(count($etablissements) > 0) {
            throw new Exception("Erreurs tous les tiers n'ont pas été réparti");
        }

        ksort($societes, SORT_NUMERIC);

        foreach($societes as $etablissements) {
            $tiers = current($etablissements);

            $societe = $this->importSociete($tiers, $etablissements);

            if(!$societe) {
                continue;
            }

            foreach($etablissements as $tiers) {
                $etablissement = $this->importEtablissement($societe, $tiers, $etablissements);
            }
        }
    }

    protected function existFamille($etablissements, $famille) {
        if($famille == EtablissementFamilles::FAMILLE_COOPERATIVE && count($etablissements)) {

            return true;
        }

        foreach($etablissements as $tiers) {
            foreach($tiers as $t) {
                if($t->getFamille() == $famille) {

                    return true;
                }
            }
        }

        return false;
    }

    /*public function fillArray() {
        $lines = file($this->file);

        foreach ($lines as $a) {
            $db2Tiers = new Db2Tiers(str_getcsv($a, ",", '"'));

            if($db2Tiers->get(Db2Tiers::COL_NO_STOCK) == $db2Tiers->get(Db2Tiers::COL_MAISON_MERE)) {
                $societes[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)] = array();
            }
        }

        foreach ($lines as $a) {
            $db2Tiers = new Db2Tiers(str_getcsv($a, ",", '"'));

            if(array_key_exists($db2Tiers->get(Db2Tiers::COL_MAISON_MERE), $societes)) {
                continue;
            }

            $societes[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)] = array();
        }

        foreach ($lines as $a) {
            $db2Tiers = new Db2Tiers(str_getcsv($a, ",", '"'));

            if(array_key_exists($db2Tiers->get(Db2Tiers::COL_NO_STOCK), $societes)) {
                $societes[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)]["00000".$db2Tiers->getFamille()][] = $db2Tiers;

                continue;
            }

            if(array_key_exists($db2Tiers->get(Db2Tiers::COL_MAISON_MERE), $societes) && !array_key_exists("00000".$db2Tiers->getFamille(), $societes[$db2Tiers->get(Db2Tiers::COL_MAISON_MERE)])) {
                $societes[$db2Tiers->get(Db2Tiers::COL_MAISON_MERE)][$db2Tiers->getFamille()][] = $db2Tiers;

                continue;
            }

            $societes[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)][$db2Tiers->getFamille()][] = $db2Tiers;
        }

        ksort($societes, SORT_NUMERIC);

        //echo count($societes);

        return;

        foreach($societes as $numSoc => $etablissements) {
            ksort($etablissements);

            $tiers = current($etablissements);

            $societe = $this->importSociete($tiers, $etablissements);

            if(!$societe) {
                continue;
            }

            $num = 1;

            foreach($etablissements as $tiers) {
                try {
                    $etablissement = $this->importEtablissement($societe, $tiers, sprintf("%02d", $num), $etablissements);
                } catch (Exception $e) {
                    continue;
                }
                $num++;
            }
        }
    }*/

    protected function importSociete($tiers, $etablissements) {
        $identifiantSociete = $this->buildIdentifiantSociete($tiers);

        if(!str_replace("C", "", $identifiantSociete)) {
            return;
        }

        $statut = SocieteClient::STATUT_ACTIF;

        if($this->isCloture($tiers)) {
            $statut = SocieteClient::STATUT_SUSPENDU;
        }

        $this->csv[] = array(
            "SOCIETE",
            null,
            "SOCIETE-".$identifiantSociete,
            $identifiantSociete,
            SocieteClient::TYPE_OPERATEUR,
            $statut,
            null,
            preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_INTITULE). ' '.$this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM))),
            null,
            $identifiantSociete,
            null,
            ($this->getInfos($tiers, Db2Tiers::COL_SIRET)),
            null,
            null,
            null,
            null,
            null,
            $this->getInfos($tiers, Db2Tiers::COL_ADRESSE_SIEGE),
            null,
            null,
            $this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_COMMUNE_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_INSEE_SIEGE),
            null,
            "FR",
            null,
            null,
            $this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ) : null,
            null,
            null,
            $this->getInfos($tiers, Db2Tiers::COL_FAX) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_FAX) ) : null,
            $this->getInfos($tiers, Db2Tiers::COL_EMAIL),
            null,
            null,
        );

        return "SOCIETE-".$identifiantSociete;
    }

    protected function importEtablissement($societe, $tiers, $societes)
    {
        $famille = $this->getFamille($tiers);
        $identifiantEtablissement = $this->buildIdentifiantEtablissement($tiers);

        $statut = EtablissementClient::STATUT_ACTIF;

        if($this->isCloture($tiers)) {
            $statut = EtablissementClient::STATUT_SUSPENDU;
        }

        if(!str_replace("C", "", $identifiantEtablissement)) {
            return null;
        }

        $email = $this->getInfos($tiers, Db2Tiers::COL_EMAIL);
        $tel = null;
        if($this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO)) {
            $tel = sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO));
        }
        $fax = null;
        if($this->getInfos($tiers, Db2Tiers::COL_FAX)) {
            $fax = sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_FAX));
        }
        if(!$email || !$tel || !$fax) {
            foreach($societes as $etablissements) {
                if(!$tel && $this->getInfos($etablissements, Db2Tiers::COL_TELEPHONE_PRO)) {
                    $tel = sprintf('%010d', $this->getInfos($etablissements, Db2Tiers::COL_TELEPHONE_PRO));
                }
                if(!$fax && $this->getInfos($etablissements, Db2Tiers::COL_FAX)) {
                    $fax = sprintf('%010d', $this->getInfos($etablissements, Db2Tiers::COL_FAX));
                }
                if(!$email && $this->getInfos($etablissements, Db2Tiers::COL_EMAIL)) {
                    $email = $this->getInfos($etablissements, Db2Tiers::COL_EMAIL);
                }
            }
        }

        $insee_declaration = ($this->getInfos($tiers, Db2Tiers::COL_INSEE_DECLARATION)) ? $this->getInfos($tiers, Db2Tiers::COL_INSEE_DECLARATION) : $this->getInfos($tiers, Db2Tiers::COL_INSEE_SIEGE);

        $intituleExploitant = $this->getInfos($tiers, Db2Tiers::COL_SEXE_CHEF_ENTR);
        $nomExploitant = trim(preg_replace('/ +/', ' ', $this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM_CHEF_ENTR)));
        if(!$nomExploitant) {
            $nomExploitant = preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM)));
        }
        $adresseExploitant = trim($this->getInfos($tiers, Db2Tiers::COL_NUMERO) . " " . $this->getInfos($tiers, Db2Tiers::COL_ADRESSE));
        if(!$adresseExploitant) {
            $adresseExploitant = $this->getInfos($tiers, Db2Tiers::COL_ADRESSE_SIEGE);
        }
        $communeExploitant = $this->getInfos($tiers, Db2Tiers::COL_COMMUNE);
        if(!$communeExploitant) {
            $communeExploitant = $this->getInfos($tiers, Db2Tiers::COL_COMMUNE_SIEGE);
        }
        $codePostalExploitant = $this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL);
        if(!$codePostalExploitant) {
            $codePostalExploitant = $this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL_SIEGE);
        }
        $telExploitant = $this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRIVE) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRIVE)) : null;
        if(!$telExploitant) {
            $telExploitant = $tel;
        }

        $dateNaissanceExploitant = sprintf("%04d-%02d-%02d", $this->getInfos($tiers, Db2Tiers::COL_ANNEE_NAISSANCE),
                                                                       $this->getInfos($tiers, Db2Tiers::COL_MOIS_NAISSANCE),
                                                                       $this->getInfos($tiers, Db2Tiers::COL_JOUR_NAISSANCE));

        if($dateNaissanceExploitant == "0000-00-00") {
            $dateNaissanceExploitant = null;
        }

        $this->csv[] = array(
            "ETABLISSEMENT",
            $societe,
            "ETABLISSEMENT-".$identifiantEtablissement,
            $this->getInfos($tiers, Db2Tiers::COL_NUM),
            $famille,
            $statut,
            $this->getInfos($tiers, Db2Tiers::COL_INTITULE),
            preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM))),
            null,
            $this->getInfos($tiers, Db2Tiers::COL_CVI),
            ($famille == EtablissementFamilles::FAMILLE_COURTIER) ? $this->getInfos($tiers, Db2Tiers::COL_NUM)  : $this->getInfos($tiers, Db2Tiers::COL_CIVABA),
            $this->getInfos($tiers, Db2Tiers::COL_SIRET),
            $this->getInfos($tiers, Db2Tiers::COL_NO_ASSICES),
            ($famille == EtablissementFamilles::FAMILLE_COURTIER) ? $this->getInfos($tiers, Db2Tiers::COL_SITE_INTERNET) : null,
            null,
            null,
            ($famille == EtablissementFamilles::FAMILLE_COURTIER) ? EtablissementClient::REGION_HORS_CVO : EtablissementClient::REGION_CVO,
            $this->getInfos($tiers, Db2Tiers::COL_ADRESSE_SIEGE),
            null,
            null,
            $this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_COMMUNE_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_INSEE_SIEGE),
            null,
            "FR",
            $insee_declaration,
            ($insee_declaration) ? $this->getCommune($insee_declaration): $etablissement->getCommune(),
            $tel,
            null,
            null,
            $fax,
            $email,
            null,
            null,
            $intituleExploitant,
            $nomExploitant,
            $adresseExploitant,
            $codePostalExploitant,
            $communeExploitant,
            "FR",
            $telExploitant,
            $dateNaissanceExploitant,
        );

        $this->csv[] = array(
            "COMPTE",
            "ETABLISSEMENT-".$identifiantEtablissement,
            "COMPTE-".$identifiantEtablissement,
            null,
            null,
            $statut,
        );

        if($this->getInfos($tiers, Db2Tiers::COL_CVI) && $identifiantEtablissement != $this->getInfos($tiers, Db2Tiers::COL_CVI)) {
            $this->csv[] = array(
                "COMPTE",
                "ETABLISSEMENT-".$identifiantEtablissement,
                "COMPTE-".$this->getInfos($tiers, Db2Tiers::COL_CVI),
                null,
                null,
                $statut,
            );
        }

        return;
    }

    protected function buildIdentifiantSociete($tiers) {
        if($this->getFamille($tiers) == EtablissementFamilles::FAMILLE_COURTIER) {

            return $this->getInfos($tiers, Db2Tiers::COL_SIRET) ? sprintf("%09d", $this->getInfos($tiers, Db2Tiers::COL_SIRET)) : null;
        }

        return $this->getInfos($tiers, Db2Tiers::COL_CVI) ? $this->getInfos($tiers, Db2Tiers::COL_CVI): "C".$this->getInfos($tiers, Db2Tiers::COL_CIVABA);
    }

    protected function buildIdentifiantEtablissement($tiers) {
        $famille = $this->getFamille($tiers);

        if($this->getFamille($tiers) == EtablissementFamilles::FAMILLE_COURTIER) {

            return $this->getInfos($tiers, Db2Tiers::COL_SIRET) ? sprintf("%09d", $this->getInfos($tiers, Db2Tiers::COL_SIRET)) : null;
        }

        return (in_array($famille, array(EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR)) && $this->getInfos($tiers, Db2Tiers::COL_CVI)) ? $this->getInfos($tiers, Db2Tiers::COL_CVI) : "C".$this->getInfos($tiers, Db2Tiers::COL_CIVABA);;
    }

    protected function isCloture($tiers) {
        foreach($tiers as $t) {
            if(!$t->isCloture()) {

                return false;
            }
        }

        return true;
    }

    protected function getFamille($tiers) {
        $famille = null;
        $producteurVinicateur = false;
        foreach($tiers as $t) {
            if($famille && $famille != $t->getFamille()) {
                throw new sfException($famille."/".$t->getFamille());
            }
            $famille = $t->getFamille();
            if($t->isProducteurVinificateur()) {
                $producteurVinicateur = true;
            }
        }

        if($producteurVinicateur) {

            return EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR;
        }

        return $famille;
    }

    protected function getInfos($tiers, $key) {
        $val = null;
        foreach($tiers as $t) {
            if($t->get($key) && $t->isRecoltant()) {

                return $t->get($key);
            }

            if($t->get($key)) {
                $val = $t->get($key);
            }
        }

        return $val;
    }

    private function getCommune($insee) {
        if (is_null($this->_insee)) {
            $csv = array();
            $this->_insee = array();
            foreach (file(sfConfig::get('sf_data_dir') . '/import/Commune') as $c) {
                $csv = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
                $this->_insee[$csv[0]] = $csv[1];
            }
        }

        if(array_key_exists($insee, $this->_insee)) {
            return $this->_insee[$insee];
        } else {
            return null;
        }
    }
}
