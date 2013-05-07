<?php

class maintenanceMiseEnLigneDeclarationTask extends sfBaseTask {

    protected function configure() {

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'MiseEnLigneDeclaration';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenanceTiers2Compte|INFO] task does things.
Call it with:

  [php symfony maintenanceTiersToCompte|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

      	$doc = acCouchdbManager::getClient()->find("CURRENT");
		
      	
      	if($doc){
      		$doc->delete();	
      	}
     	      	
      	$json = new stdClass();
      	$json->_id = "CURRENT";
		$json->campagne	= "2012";
		$json->dr_non_editable= "0";
		$json->dr_non_ouverte = "0";
		$json->type = "Current";
      	
		$doc = acCouchdbManager::getClient()->createDocumentFromData($json);
        $doc->save();
       

    }

}
