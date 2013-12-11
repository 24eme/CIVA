<?php

class DRDateModificationTask extends sfBaseTask
{

    protected function configure()
    {
        $this->addArguments(array(
          new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'id'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'dr';
        $this->name = 'date-modification';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {

      // initialize the database connection
      $databaseManager = new sfDatabaseManager($this->configuration);
      $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
      
      $dr = DRClient::getInstance()->find($arguments['id']);

      if(!$dr) {
        echo "ERREUR;".$dr->_id.";La DR n'existe pas\n";
        return;
      }

      if(!$dr->isValideeCiva()) {

        return;
      }

      if($dr->validee == $dr->modifiee) {
        return;
      }

      $dr->modifiee = $dr->validee;
      
      echo "INFO;".$dr->_id.";Date de modification changé à la date de validation\n";

      $dr->save();

    }
    
}

