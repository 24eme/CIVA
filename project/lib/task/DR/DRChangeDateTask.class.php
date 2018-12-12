<?php

class DRChangeDateTask extends sfBaseTask
{

    protected function configure()
    {
        $this->addArguments(array(
          new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'id'),
          new sfCommandArgument('date', sfCommandArgument::REQUIRED, 'date'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'dr';
        $this->name = 'changeDate';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {

      // initialize the database connection
      $databaseManager = new sfDatabaseManager($this->configuration);
      $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
      
      $dr = DRClient::getInstance()->find($arguments['id']);

      $date = $arguments['date'];

      if(!$dr) {
        echo "ERREUR;".$dr->_id.";La DR n'existe pas\n";
        return;
      }

      if(!$dr->isValideeCiva()) {

        return;
      }

      if($dr->validee <= $date) {
        return;
      }

      $dr->validee = $date;
      $dr->modifiee = $date;

      echo "INFO;".$dr->_id.";Date de validation passé au ".$date."\n";

      $dr->save();
    }

}
