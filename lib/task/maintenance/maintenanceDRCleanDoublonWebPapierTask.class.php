<?php

class maintenanceDRCleanDoublonWebPapierTask extends sfBaseTask {

    protected function configure() {
		$this->addArguments(array(
			new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'CSV'),
		));
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'dr-clean-doublon';
        $this->briefDescription = 'import csv dr file';
        $this->detailedDescription = <<<EOF
The [import|INFO] task does things.
Call it with:

  [php symfony import|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        set_time_limit('3600');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
	    
        $nb_item = 0;
	    $nb_deleted = 0;
	    if (isset($arguments['file']) && !empty($arguments['file'])) {
    	if (file_exists($arguments['file'])) {
	        foreach (file($arguments['file']) as $numero => $ligne) {
	        	$datas = explode(';', $ligne);
	        	$value = $datas[0];
	    		$dr = sfCouchdbManager::getClient('DR')->retrieveDocumentById($value);
	    		if ($dr) {
	    			$this->logSection('Doublons have been detected', $dr->_id);
	    		}
	        }
    	}
	    }
    }

}
