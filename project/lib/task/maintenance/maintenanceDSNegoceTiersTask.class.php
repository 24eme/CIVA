<?php

class maintenanceDSNegoceTiersTask extends sfBaseTask
{
  protected function configure()
  {

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'export cvi for a campagne', '2010'),
      // add your own options here
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'ds-negoce-tiers';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set("memory_limit", "512M");
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $ids_compte = acCouchdbManager::getClient("_Compte")->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    foreach ($ids_compte as $id) {
            $compte = acCouchdbManager::getClient()->find($id);

            if(!$compte instanceof CompteTiers) {
              continue;
            }

            if(!$compte->hasDroit(_CompteClient::DROIT_DS_DECLARANT)) {
              continue;
            }

            $declarant = $compte->getDeclarantDS(DSCivaClient::TYPE_DS_PROPRIETE);
            if($declarant && $declarant->exist('ds_decembre') && $declarant->ds_decembre) {
              echo $declarant->_id.";PROPRIETE;".$declarant->nom.";".$declarant->categorie.";".$declarant->commune."\n";
            }

            $declarant = $compte->getDeclarantDS(DSCivaClient::TYPE_DS_NEGOCE);
            if($declarant && $declarant->exist('ds_decembre') && $declarant->ds_decembre) {
              echo $declarant->_id.";NEGOCE;".$declarant->nom.";".$declarant->categorie.";".$declarant->commune."\n";
            }
    }

  }
}
