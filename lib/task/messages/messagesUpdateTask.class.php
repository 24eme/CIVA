<?php

class messagesUpdateTask extends sfBaseTask
{
  protected function configure()
  {
	
	$this->addArguments(array(
		new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'Update from file'),
	));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'messages';
    $this->name             = 'update';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [messagesUpdateTask|INFO] task does things.
Call it with:

  [php symfony messagesUpdateTask|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    if (file_exists($arguments['file'])) {
        foreach (file($arguments['file']) as $numero => $ligne) {
        	$datas = explode(';', $ligne);
        	$field = $this->getCsvValueAfterTreatment($datas[0]);
        	$value = $this->getCsvValueAfterTreatment($datas[1]);
        	$messages = sfCouchdbManager::getClient('Messages')->retrieveMessages();
        	if ($messages->exist($field)) {
        		$messages->set($field,$value);
        		$this->logSection("ligne ".$numero, "update success", null);
        	} else {
        		$messages->add($field, $value);
        		$this->logSection("ligne ".$numero, $field." doesn't exist, it was created", null, 'ERROR');
        	}
        	$messages->save();
        }
    	
    } else {
    	$this->logSection("update", "the file given can not be found", null, 'ERROR');
    }
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
