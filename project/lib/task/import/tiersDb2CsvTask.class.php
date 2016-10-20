<?php

class tiersDb2CsvTask extends sfBaseTask
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

    $this->namespace        = 'tiers';
    $this->name             = 'db2-csv';
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

            /*foreach ($societe->contacts as $idCompte => $compte) {
                $contact = CompteClient::getInstance()->find($idCompte);
                $contact->setStatut($societe->getStatut());
                //$contact->save();
            }*/

            //echo "------------------------\n";
        }
    }

    protected function importSociete($tiers, $etablissements) {
        $identifiantSociete = $this->buildIdentifiantSociete($tiers);

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

        echo implode(";", array(
            "SOCIETE",
            null,
            $identifiantSociete,
            SocieteClient::TYPE_OPERATEUR,
            $statut,
            "",
            preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_INTITULE). ' '.$this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM))),
            "",
            "",
            ($this->getInfos($tiers, Db2Tiers::COL_SIRET)),
            "",
            "",
            $this->getInfos($tiers, Db2Tiers::COL_ADRESSE_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_COMMUNE_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_INSEE_SIEGE),
            "FR",
            null,
            null,
            $this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ) : null,
            null,
            $this->getInfos($tiers, Db2Tiers::COL_FAX) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_FAX) ) : null,
            $this->getInfos($tiers, Db2Tiers::COL_EMAIL),
        ))."\n";

        return "SOCIETE-".$identifiantSociete;
    }

    protected function importEtablissement($societe, $tiers, $num)
    {
        $famille = $this->getFamille($tiers);
        $identifiantEtablissement = $this->buildIdentifiantEtablissement($tiers);

        $statut = EtablissementClient::STATUT_ACTIF;

        if($this->isCloture($tiers)) {
            $statut = EtablissementClient::STATUT_SUSPENDU;
        }

        if(!str_replace("C", "", $identifiantEtablissement)) {
            echo "Pas d'identifiant ".$this->getInfos($tiers, Db2Tiers::COL_NUM);
            return null;
        }

        $insee_declaration = ($this->getInfos($tiers, Db2Tiers::COL_INSEE_DECLARATION)) ? $this->getInfos($tiers, Db2Tiers::COL_INSEE_DECLARATION) : $this->getInfos($tiers, Db2Tiers::COL_INSEE_SIEGE);

        echo implode(";", array(
            "ETABLISSEMENT",
            $societe,
            $identifiantEtablissement,
            $famille,
            $statut,
            $this->getInfos($tiers, Db2Tiers::COL_INTITULE),
            preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM))),
            $this->getInfos($tiers, Db2Tiers::COL_CVI),
            $this->getInfos($tiers, Db2Tiers::COL_CIVABA),
            $this->getInfos($tiers, Db2Tiers::COL_SIRET),
            $this->getInfos($tiers, Db2Tiers::COL_NO_ASSICES),
            ($famille == EtablissementFamilles::FAMILLE_COURTIER) ? $this->getInfos($tiers, Db2Tiers::COL_SITE_INTERNET) : null,
            $this->getInfos($tiers, Db2Tiers::COL_ADRESSE_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_COMMUNE_SIEGE),
            $this->getInfos($tiers, Db2Tiers::COL_INSEE_SIEGE),
            "FR",
            $insee_declaration,
            ($insee_declaration) ? $this->getCommune($insee_declaration): $etablissement->getCommune(),
            $this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO)) : null,
            null,
            $this->getInfos($tiers, Db2Tiers::COL_FAX) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_FAX) ) : null,
            $this->getInfos($tiers, Db2Tiers::COL_EMAIL),
        ))."\n";

        $nom = trim(preg_replace('/ +/', ' ', $this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM_CHEF_ENTR)));
        if(!$nom) {
            $nom = preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM)));
        }

        $adresse = trim($this->getInfos($tiers, Db2Tiers::COL_NUMERO) . " " . $this->getInfos($tiers, Db2Tiers::COL_ADRESSE));
        if(!$adresse) {
            $adresse = $this->getInfos($tiers, Db2Tiers::COL_ADRESSE_SIEGE);
        }
        $commune = $this->getInfos($tiers, Db2Tiers::COL_COMMUNE);
        if(!$commune) {
            $commune = $this->getInfos($tiers, Db2Tiers::COL_COMMUNE_SIEGE);
        }
        $codePostal = $this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL);
        if(!$commune) {
            $codePostal = $this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL_SIEGE);
        }
        echo implode(";", array(
            "INTERLOCUTEUR",
            $societe,
            $identifiantEtablissement."01",
            null,
            $statut,
            $this->getInfos($tiers, Db2Tiers::COL_SEXE_CHEF_ENTR),
            $nom,
            null,
            null,
            null,
            null,
            null,
            $adresse,
            $commune,
            $codePostal,
            null,
            "FR",
            null,
            null,
            null,
            $this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRIVE) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRIVE) ) : null,
            null,
            null,
        ))."\n";

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

    protected function createCompteLS($id, $idReference) {
        $ls = new LS();
        $ls->set('_id', $id);
        $ls->pointeur = $idReference;
        //$ls->save();

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
                //echo "-------diff:".$key.":(".$num.")".$val."/(".$t->get(Db2Tiers::COL_NUM).")".$t->get($key)."\n";
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
