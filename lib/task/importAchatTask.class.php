<?php

class importAchatTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
            new sfCommandOption('output', null, sfCommandOption::PARAMETER_REQUIRED, 'output type [json|none]', 'json'),
        ));

        $this->namespace = 'import';
        $this->name = 'Achat';
        $this->briefDescription = 'import csv achat file';
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
	
	$docs = array();

        foreach (file(sfConfig::get('sf_data_dir') . '/' . 'Achat09') as $a) {
	  $json = new stdClass();
	  $achat = explode(',', preg_replace('/"/', '', $a));
	  if (!isset($achat[2]) || !$achat[2] || !strlen($achat[2]))
	    continue;

	  $json->_id = 'ACHAT-'.$achat[2];
	  $json->cvi = $achat[2];
	  $json->civaba = $achat[1];
	  $json->type = "Acheteur";
	  $json->qualité = ($achat[4] == 'N') ? 'Negociant' : 'Cooperative';
	  $json->nom = rtrim(preg_replace('/\s{4}\s*/', ', ', $achat[5]));
	  $json->commune = rtrim($achat[6]);
	  $json->achnum = $achat[0];
	  $docs[] = $json;
	}
	if ($options['output'] == 'json') {
	  echo '{"docs":';
	  echo json_encode($docs);
	  echo '}';
	  return ;
	}
	foreach ($docs as $data) {
	  $doc = sfCouchdbManager::getClient()->createDocumentFromData($data);
	  $doc->save();
	}
    }

    private function recode_number($val) {
        return preg_replace('/^\./', '0.', $val) + 0;
    }

}
