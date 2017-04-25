<?php

class acCouchdbDocumentSetValueTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'ID du document'),
       new sfCommandArgument('hash', sfCommandArgument::REQUIRED, 'Hash'),
       new sfCommandArgument('value', sfCommandArgument::REQUIRED, 'Value'),
       new sfCommandArgument('type', sfCommandArgument::OPTIONAL, 'Type', "string"),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      new sfCommandOption('remove', null, sfCommandOption::PARAMETER_REQUIRED, 'Supprime la hash', false),
      // add your own options here
    ));

    $this->namespace        = 'document';
    $this->name             = 'setvalue';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenanceCompteStatut|INFO] task does things.
Call it with:

  [php symfony maintenanceCompteStatut|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $doc = acCouchdbManager::getClient()->find($arguments['doc_id']);

    if($options['remove']) {
        $doc->remove($arguments['hash']);
    } else {
        $doc->add($arguments['hash'], settype($arguments['value'], $arguments['type']));
    }
    $doc->save();

    if($options['remove']) {
        echo "Document ".$doc->_id." removed ".$arguments['hash'] ."\n";
    } else {
        echo "Document ".$doc->_id." set ".$arguments['hash']. " = ".$arguments['value']."\n";
    }
  }
}
