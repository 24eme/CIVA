<?php

class DRCheckTask extends sfBaseTask
{

    protected function configure()
    {
        $this->addArguments(array(
          new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'id'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'dr';
        $this->name = 'check';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [dr:check|INFO] task does things.
Call it with:

  [php symfony dr:check|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        sfContext::createInstance($this->configuration);
      // initialize the database connection
      $databaseManager = new sfDatabaseManager($this->configuration);
      $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

      $dr = DRClient::getInstance()->find($arguments['id']);

      foreach($dr->check() as $type => $erreurs) {
          foreach($erreurs as $erreur) {
              echo $dr->_id.";".$type.";".$erreur['log'].";".$erreur['info'].";".$erreur['url']."\n";
          }
      }
    }

}
