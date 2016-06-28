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
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $nb_not_use = 0;

        $societes = array();

        $lines = file($arguments['file']);

        foreach ($lines as $a) {
            $db2Tiers = new Db2Tiers(explode(',', preg_replace('/"/', '', preg_replace('/[^"]+$/', '', $a))));

            if($db2Tiers->get(Db2Tiers::COL_NO_STOCK) == $db2Tiers->get(Db2Tiers::COL_MAISON_MERE)) {
                $societes[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)] = array();
            }
        }

        foreach ($lines as $a) {
            $db2Tiers = new Db2Tiers(explode(',', preg_replace('/"/', '', preg_replace('/[^"]+$/', '', $a))));

            if(array_key_exists($db2Tiers->get(Db2Tiers::COL_MAISON_MERE), $societes)) {
                continue;
            }

            $societes[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)] = array();
        }

        foreach ($lines as $a) {
            $db2Tiers = new Db2Tiers(explode(',', preg_replace('/"/', '', preg_replace('/[^"]+$/', '', $a))));

            if(array_key_exists($db2Tiers->get(Db2Tiers::COL_NO_STOCK), $societes)) {
                $societes[$db2Tiers->get(Db2Tiers::COL_NO_STOCK)][$db2Tiers->get(Db2Tiers::COL_NO_STOCK)][] = $db2Tiers;
                continue;
            }

            if(array_key_exists($db2Tiers->get(Db2Tiers::COL_MAISON_MERE), $societes)) {
                $societes[$db2Tiers->get(Db2Tiers::COL_MAISON_MERE)][$db2Tiers->get(Db2Tiers::COL_NO_STOCK)][] = $db2Tiers;
                continue;
            }
        }

        foreach($societes as $numSoc => $etablissements) {
            echo count($etablissements)." Etablissement(s)\n";
            $tiers = $etablissements[$numSoc];


            $identifiantSociete = $this->getInfos($tiers, Db2Tiers::COL_CVI);

            if(!$identifiantSociete) {
                continue;
            }

            $societe = SocieteClient::getInstance()->find("SOCIETE-".$identifiantSociete);

            if(!$societe) {
                $societe = new Societe();
                $societe->setIdentifiant($identifiantSociete);
            }

            $societe->setRaisonSociale(preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_INTITULE). ' '.$this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM))));
            $societe->setSiret($this->getInfos($tiers, Db2Tiers::COL_SIRET));

            $societe->setAdresse($this->getInfos($tiers, Db2Tiers::COL_ADRESSE_SIEGE));
            $societe->setCodePostal($this->getInfos($tiers, Db2Tiers::COL_CODE_POSTAL_SIEGE));
            $societe->setCommune($this->getInfos($tiers, Db2Tiers::COL_COMMUNE_SIEGE));
            $societe->setInsee($this->getInfos($tiers, Db2Tiers::COL_INSEE_SIEGE));
            $societe->setTelephoneBureau($this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRO) ) : null);
            $societe->setTelephonePerso($this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRIVE) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_TELEPHONE_PRIVE) ) : null);
            $societe->setFax($this->getInfos($tiers, Db2Tiers::COL_FAX) ? sprintf('%010d',$this->getInfos($tiers, Db2Tiers::COL_FAX) ) : null);
            $societe->setEmail($this->getInfos($tiers, Db2Tiers::COL_EMAIL));

            $societe->save();

            $identifiantEtablissement = $societe->getIdentifiant()."01";

            $etablissement = EtablissementClient::getInstance()->find("ETABLISSEMENT-".$identifiantEtablissement);

            if(!$etablissement) {
                $etablissement = new Etablissement();
                $etablissement->setIdSociete($societe->_id);
                $etablissement->setIdentifiant($societe->getIdentifiant()."01");
                $etablissement->save();
                $societe->pushContactAndAdresseTo($etablissement);
            }

            $etablissement->setIntitule($this->getInfos($tiers, Db2Tiers::COL_INTITULE));
            $etablissement->setNom(preg_replace('/ +/', ' ', trim($this->getInfos($tiers, Db2Tiers::COL_NOM_PRENOM))));
            $etablissement->setNumInterne($this->getInfos($tiers, Db2Tiers::COL_CIVABA));
            $etablissement->setCvi($this->getInfos($tiers, Db2Tiers::COL_CVI));
            $etablissement->setNoAccises($this->getInfos($tiers, Db2Tiers::COL_NO_ASSICES));
            $etablissement->setFamille($tiers[0]->getFamille());

            $etablissement->save();
            echo $societe->_id."\n";
            foreach($societe->getEtablissements() as $id => $null) {
                echo $id."\n";
            }
            foreach($societe->getContacts() as $id => $null) {
                echo $id."\n";
            }

            echo "\n";

        }

    }

    protected function getInfos($tiers, $key) {
        foreach($tiers as $t) {
            if($t->get($key)) {

                return $t->get($key);
            }
        }

        return null;
    }

    protected function resolveIdentifiantSociete($etablissements) {
        $printed = false;
        foreach($etablissements as $tiers) {
            foreach($tiers as $t) {
                if($t->get(Db2Tiers::COL_CVI)) {
                    //echo (($printed) ? "PLUSIEURS" : "").$t->get(Db2Tiers::COL_CVI)."\n";
                    $printed = true;
                }
            }
        }
    }

}
