<?php

class maintenanceMigrationLieuxStockagesTask extends sfBaseTask
{
    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
           new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'Tiers doc id'),
        ));

        $this->addOptions(array(
          new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
          new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
          new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
          // add your own options here
        ));

        $this->namespace        = 'maintenance';
        $this->name             = 'migration-lieux-stockage';
        $this->briefDescription = '';
        $this->detailedDescription = '';
    }

    protected function execute($arguments = array(), $options = array())
    {
        //initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $tiers = _TiersClient::getInstance()->find($arguments['doc_id'], acCouchdbClient::HYDRATE_JSON);

        if(!$tiers) {

            throw new sfException("La tiers n'existe pas ".$arguments['doc_id']);
        }

        if(!isset($tiers->lieux_stockage) || !count($tiers->lieux_stockage)) {
            return;
        }


        $etablissement = null;
        $identifiantLieuxStockage = null;
        if(preg_match("/REC-/", $tiers->_id)) {
            $etablissement = EtablissementClient::getInstance()->find(str_replace("REC-", "", $tiers->_id));
            $identifiantLieuxStockage = $etablissement->getIdentifiant();
        }

        if(preg_match("/MET-/", $tiers->_id)) {
            $etablissement = EtablissementClient::getInstance()->find(str_replace("MET-", "C", $tiers->_id));
            $identifiantLieuxStockage = $etablissement->getIdentifiant();
        }

        if(preg_match("/ACHAT-/", $tiers->_id)) {
            $etablissement = EtablissementClient::getInstance()->find("C".$tiers->civaba);
            $identifiantLieuxStockage =  $tiers->cvi;
        }

        if(!$etablissement && $tiers->statut == "INACTIF") {

            return;
        }

        if(!$etablissement) {
            throw new sfException("L'établissement correspondant au tiers ".$arguments['doc_id']." n'existe pas");
        }
        if(count($etablissement->getLieuxStockage(false, $identifiantLieuxStockage)) > 0) {
            return;
        }
        foreach($tiers->lieux_stockage as $key => $lieu_stockage) {
            $etablissement->add("lieux_stockage")->add($key, $lieu_stockage);
        }
        $etablissement->save();
        echo "Etablissement ".$etablissement->_id." : ".count($etablissement->lieux_stockage)." lieu(x) de stockage(s) importé(s)\n";
    }


}