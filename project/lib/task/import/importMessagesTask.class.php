<?php

class importMessagesTask extends sfBaseTask {

    protected function configure() {

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
            new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'couchdb'),
            new sfCommandOption('removedb', null, sfCommandOption::PARAMETER_REQUIRED, '= yes if remove the db debore import [yes|no]', 'no'),
            new sfCommandOption('year', null, sfCommandOption::PARAMETER_REQUIRED, 'year version of the file to be imported', '09'),
        ));

        $this->namespace = 'import';
        $this->name = 'Messages';
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
/*		
	if($options['removedb'] == 'yes' && $options['import'] == 'couchdb') {
	  if (sfCouchdbManager::getClient()->databaseExists()) {
	    sfCouchdbManager::getClient()->deleteDatabase();
	  }
	  sfCouchdbManager::getClient()->createDatabase();
	}
*/
	$docs = array();

	$json = new stdClass();
	$json->_id = 'MESSAGES';
	$json->type = 'Messages';

    /* Mise a jour des messages si un csv est passé en argument */

        $fichier_messages = sfConfig::get('sf_data_dir').'/configuration/messages.csv';

        if (isset($fichier_messages) && !empty($fichier_messages)) {
    	if (file_exists($fichier_messages)) {
	        foreach (file($fichier_messages) as $numero => $ligne) {
	        	$datas = explode(';', $ligne);
	        	$field = $datas[0];
	        	$value = $datas[1];

                if (isset($field)) {
	        		$this->logSection("ligne ".($numero + 1), "import message success", null);
	        	}
                $json->{$field} = $value;
	        } 
    	} else {
    		$this->logSection("import_message", "the file given can not be found", null, 'ERROR');
    	}
    }

	$docs[] = $json;

	if ($options['import'] == 'couchdb') {
	  foreach ($docs as $data) {
	    $doc = sfCouchdbManager::getClient()->retrieveDocumentById($data->_id);
	    if ($doc) {
	      $doc->delete();
	    }
            $doc = sfCouchdbManag er::getClient()->createDocumentFromData($data);
	    $doc->save();
	  }
	  return;
	}
	echo '{"docs":';
	echo json_encode($docs);
	echo '}';
    }
  protected function deleteFirstAndLastCharacter($string) 
  {
  	$string = substr($string, 0, -1); // delete last
  	$string = substr($string, 1); // delete first
  	return $string;
  }
  protected function getCsvValueAfterTreatment($string)
  {
  	$string = trim($string);
  	if (strlen($string) > 2) {
  		$string = $this->deleteFirstAndLastCharacter($string);
  	}
  	return $string;
  }
}
