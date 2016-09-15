<?php

class importTiersMigrationTask extends sfBaseTask
{

  protected $_insee = null;

  protected function configure()
  {
     $this->addArguments(array(
       new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'My import from file'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'import';
    $this->name             = 'TiersMigration';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [importTiers3|INFO] task does things.
Call it with:

  [php symfony importTiers3|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $nb_not_use = 0;

        $societes = array();

        $lines = file($arguments['file']);

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
                    $etablissement = $this->importEtablissement($societe, $tiers, sprintf("%02d", $num));
                } catch (Exception $e) {
                    echo "ERROR;".$societe->_id.";".$e->getMessage()."\n";
                    continue;
                }
                $num++;
            }

            echo "------------------------\n";
        }
    }

    protected function importSociete($tiers, $etablissements) {
        $identifiantSociete = $this->getInfos($tiers, Db2Tiers::COL_CVI) ? $this->getInfos($tiers, Db2Tiers::COL_CVI): "C".$this->getInfos($tiers, Db2Tiers::COL_CIVABA);

        if(!str_replace("C", "", $identifiantSociete)) {
            return;
        }

        $statut = SocieteClient::STATUT_ACTIF;

        if($this->isCloture($tiers)) {
            $statut = SocieteClient::STATUT_SUSPENDU;
            /*if(!_CompteClient::getInstance()->find("COMPTE-".$identifiantSociete, acCouchdbClient::HYDRATE_JSON)) {
                 return;
            }*/
        }

        $societe = SocieteClient::getInstance()->find("SOCIETE-".$identifiantSociete);

        if(!$societe) {
            $societe = new Societe();
            $societe->setIdentifiant($identifiantSociete);
            $societe->setTypeSociete(SocieteClient::TYPE_OPERATEUR);
            $societe->constructId();
            $compte = $societe->createCompteSociete($societe->getIdentifiant());
            $compte->constructId();
            $societe->setCompteSocieteObject($compte);
        }

        $societe->setRaisonSociale(preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_INTITULE). ' '.$this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM))));
        $societe->setSiret($this->getInfos($tiers, Db2Tiers::COL_SIRET));

        $societe->setAdresse($this->getInfos($tiers, Db2Tiers::COL_ADRESSE_SIEGE));
        $societe->setCodePostal($this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL_SIEGE));
        $societe->setCommune($this->getInfos($tiers, Db2Tiers::COL_COMMUNE_SIEGE));
        $societe->setInsee($this->getInfos($tiers, Db2Tiers::COL_INSEE_SIEGE));
        $societe->setPays("FR");
        $societe->setStatut($statut);
        $societe->setTelephoneBureau($this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ) : null);
        $societe->setFax($this->getInfos($tiers, Db2Tiers::COL_FAX) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_FAX) ) : null);
        $societe->setEmail($this->getInfos($tiers, Db2Tiers::COL_EMAIL));

        try {
            $societe->save();
        } catch (Exception $e) {
            echo "ERROR;".$e->getMessage().";".$this->getInfos($tiers, Db2Tiers::COL_NUM)."\n";
            return;
        }

            echo $societe->_id." (".$societe->getRaisonSociale()." ".$societe->getStatut().") avec le compte ".$societe->getCompteSociete()." ".count($etablissements)." etablissements "."\n";

        return $societe;
    }

    protected function importEtablissement($societe, $tiers, $num)
    {
        $famille = $this->getFamille($tiers);
        $identifiantEtablissement = (in_array($famille, array(EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR)) && $this->getInfos($tiers, Db2Tiers::COL_CVI)) ? $this->getInfos($tiers, Db2Tiers::COL_CVI) : "C".$this->getInfos($tiers, Db2Tiers::COL_CIVABA);

        if(!str_replace("C", "", $identifiantEtablissement)) {
            echo "Pas d'identifiant ".$this->getInfos($tiers, Db2Tiers::COL_NUM);
            return null;
        }

        $etablissement = EtablissementClient::getInstance()->find("ETABLISSEMENT-".$identifiantEtablissement);

        $statut = EtablissementClient::STATUT_ACTIF;

        if($this->isCloture($tiers)) {
            $statut = EtablissementClient::STATUT_SUSPENDU;
            // if(!_CompteClient::getInstance()->find("COMPTE-".$identifiantEtablissement)) {
            //     return;
            // }
        }

        if(!$etablissement) {
            $etablissement = new Etablissement();
            $etablissement->setIdSociete($societe->_id);
            $etablissement->setIdentifiant($identifiantEtablissement);
            $etablissement->setFamille($tiers[0]->getFamille());
            $etablissement->constructId();
        }

        if(!$etablissement->getCompte() && !CompteClient::getInstance()->find("COMPTE-".$etablissement->getIdentifiant())) {
            $compte = CompteClient::getInstance()->createCompteFromEtablissement($etablissement);
            $compte->addOrigine($etablissement->_id);
            $compte->setIdentifiant($etablissement->getIdentifiant());
            $compte->constructId();
            $etablissement->setCompte($compte->_id);
            try {
                $compte->save();
            } catch (Exception $e) {
                echo "ERROR;".$e->getMessage().";".$this->getInfos($tiers, Db2Tiers::COL_NO_STOCK)."\n";
                return;
            }
        } else {
            $etablissement->setCompte("COMPTE-".$etablissement->getIdentifiant());
        }

        $etablissement->setIntitule($this->getInfos($tiers, Db2Tiers::COL_INTITULE));
        $etablissement->setNom(preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM))));
        $etablissement->setNumInterne($this->getInfos($tiers, Db2Tiers::COL_CIVABA));
        $etablissement->setCvi($this->getInfos($tiers, Db2Tiers::COL_CVI));
        $etablissement->setNoAccises($this->getInfos($tiers, Db2Tiers::COL_NO_ASSICES));
        $etablissement->setFamille($this->getFamille($tiers));
        $etablissement->setStatut($statut);
        $etablissement->setSiret($this->getInfos($tiers, Db2Tiers::COL_SIRET));
        $etablissement->setAdresse($this->getInfos($tiers, Db2Tiers::COL_ADRESSE_SIEGE));
        $etablissement->setCodePostal($this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL_SIEGE));
        $etablissement->setCommune($this->getInfos($tiers, Db2Tiers::COL_COMMUNE_SIEGE));
        $etablissement->setInsee($this->getInfos($tiers, Db2Tiers::COL_INSEE_SIEGE));
        $etablissement->setPays("FR");
        $etablissement->setTelephoneBureau($this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ) : null);
        $etablissement->setFax($this->getInfos($tiers, Db2Tiers::COL_FAX) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_FAX) ) : null);
        $etablissement->setEmail($this->getInfos($tiers, Db2Tiers::COL_EMAIL));
        $etablissement->add('declaration_insee', ($this->getInfos($tiers, Db2Tiers::COL_INSEE_DECLARATION)) ? $this->getInfos($tiers, Db2Tiers::COL_INSEE_DECLARATION) : $etablissement->getInsee());
        $etablissement->add('declaration_commune', ($etablissement->declaration_insee) ? $this->getCommune($etablissement->declaration_insee    ): $etablissement->getCommune());

        try {
            $etablissement->save();
        } catch (Exception $e) {
            echo "ERROR;".$e->getMessage().";".$this->getInfos($tiers, Db2Tiers::COL_NO_STOCK)."\n";
            return;
        }

        $compteExploitant = $etablissement->getCompteExploitantObject();
        if(!$compteExploitant) {
            $compteExploitant = CompteClient::getInstance()->createCompteFromSociete($societe);
            $compteExploitant->setIdentifiant($etablissement->getIdentifiant()."01");
            $compteExploitant->constructId();

            $etablissement->setCompteExploitant($compteExploitant->_id);
            try {
                $etablissement->save();
            } catch (Exception $e) {
                echo "ERROR;".$e->getMessage().";".$this->getInfos($tiers, Db2Tiers::COL_NO_STOCK)."\n";
                return;
            }
        }

        $compteExploitant->setCivilite($this->getInfos($tiers, Db2Tiers::COL_SEXE_CHEF_ENTR));
        $nom = trim(preg_replace('/ +/', ' ', $this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM_CHEF_ENTR)));
        $compteExploitant->setNom(($nom) ? $nom : $etablissement->getNom());
        $adresse = trim($this->getInfos($tiers, Db2Tiers::COL_NUMERO) . " " . $this->getInfos($tiers, Db2Tiers::COL_ADRESSE));
        $compteExploitant->setAdresse(($adresse) ? $adresse : $etablissement->getAdresse());
        $commune = $this->getInfos($tiers, Db2Tiers::COL_COMMUNE);
        $compteExploitant->setCommune(($commune) ? $commune : $etablissement->getCommune());
        $codePostal = $this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL);
        $compteExploitant->setCodePostal(($codePostal) ? $codePostal : $etablissement->getCodePostal());
        $compteExploitant->setTelephonePerso($this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRIVE) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRIVE) ) : null);

        try {
            $compteExploitant->save();
        } catch (Exception $e) {
            echo "ERROR;".$e->getMessage().";".$this->getInfos($tiers, Db2Tiers::COL_NO_STOCK)."\n";
            return;
        }

        echo $etablissement->_id." (".$etablissement->getIntitule() ." ". $etablissement->getNom().", ".$etablissement->getFamille().", ".$this->getInfos($tiers, Db2Tiers::COL_SIRET)." ".$etablissement->getStatut().") avec le compte ".$etablissement->getCompte()." et le compte exploitant ".$compteExploitant->_id." ".count($tiers)." tiers, Num : ".$this->getInfos($tiers, Db2Tiers::COL_NUM)." Num Stock : ".$this->getInfos($tiers, Db2Tiers::COL_NO_STOCK)." Num maison mère : ".$this->getInfos($tiers, Db2Tiers::COL_MAISON_MERE)." ".$this->getInfos($tiers, Db2Tiers::COL_TYPE_TIERS)."\n";

        if($etablissement->getCvi() && $etablissement->getIdentifiant() != $etablissement->getCvi() && !CompteClient::getInstance()->find("COMPTE-".$etablissement->getCvi(), acCouchdbClient::HYDRATE_JSON)) {
            $this->createCompteLS("COMPTE-".$etablissement->getCvi(), $etablissement->getMasterCompte()->_id);
        }

        return $etablissement;
    }

    protected function createCompteLS($id, $idReference) {
        $ls = new LS();
        $ls->set('_id', $id);
        $ls->pointeur = $idReference;
        $ls->save();

        echo "Compte annexe ".$ls->_id."\n";
    }

    protected function isCloture($tiers) {
        foreach($tiers as $t) {
            if($t->isCloture()) {

                return true;
            }
        }

        return false;
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
        $num = null;
        foreach($tiers as $t) {
            if($val && $t->get($key) && $val != $t->get($key)) {
                echo "-------diff:".$key.":(".$num.")".$val."/(".$t->get(Db2Tiers::COL_NUM).")".$t->get($key)."\n";
            }

            if($t->get($key) && $t->isRecoltant()) {

                return $t->get($key);
            }

            if($t->get($key)) {
                $val = $t->get($key);
            }
            $num = $t->get(Db2Tiers::COL_NUM);

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
