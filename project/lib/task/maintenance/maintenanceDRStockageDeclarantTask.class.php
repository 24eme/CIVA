<?php

class maintenanceDRStockageDeclarantTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
          new sfCommandArgument('id', sfCommandArgument::REQUIRED),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'dr-stockage-declarant';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $dr = acCouchdbManager::getClient()->find($arguments['id'], acCouchdbClient::HYDRATE_JSON);

        if($dr->type != "DR") {
          return;
        }
        
        $tiers = acCouchdbManager::getClient()->find('REC-'.$dr->cvi);

        if(!$tiers) {
          echo "ERREUR;TIERS INTROUVABLE;".$dr->cvi."\n";
          return;
        }

        if(!isset($dr->declarant)) {
          $dr->declarant = new stdClass();
        }

        if(!isset($dr->declarant->nom)) {
          $dr->declarant->nom = null;
          if ($tiers->exist("intitule") && $tiers->get("intitule")) {
            $dr->declarant->nom = $tiers->intitule . " ";
          }
          $dr->declarant->nom .= $tiers->nom;
        }

        if(!isset($dr->declarant->telephone)) {
          $dr->declarant->telephone = $tiers->telephone;
        }

        $dr->declarant->raison_sociale = $tiers->getRaisonSociale();
        $dr->declarant->cvi = $tiers->cvi;
        $dr->declarant->no_accises = $tiers->getNoAccises();
        $dr->declarant->adresse = $tiers->siege->adresse;
        $dr->declarant->commune = $tiers->siege->commune;
        $dr->declarant->code_postal = $tiers->siege->code_postal;
        $dr->declarant->region = $tiers->getRegion();
        $dr->declarant->siret = $tiers->siret;
        $dr->declarant->fax = $tiers->fax;

        $dr->identifiant = $dr->cvi;
        $dr->declarant->exploitant = new stdClass();
        $dr->declarant->exploitant->sexe = $tiers->exploitant->sexe;
        $dr->declarant->exploitant->nom = $tiers->exploitant->nom;
        $dr->declarant->exploitant->adresse = $tiers->exploitant->adresse;
        $dr->declarant->exploitant->code_postal = $tiers->exploitant->code_postal;
        $dr->declarant->exploitant->commune = $tiers->exploitant->commune;
        $dr->declarant->exploitant->date_naissance = $tiers->exploitant->date_naissance;
        $dr->declarant->exploitant->telephone = $tiers->exploitant->telephone;

        acCouchdbManager::getClient()->storeDoc($dr);

        echo $dr->_id."\n";
    }

}
