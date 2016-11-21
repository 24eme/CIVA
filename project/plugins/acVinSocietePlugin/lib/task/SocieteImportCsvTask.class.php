<?php

class SocieteImportCsvTask extends sfBaseTask
{

  protected function configure()
  {
     $this->addArguments(array(
        new sfCommandArgument('file', sfCommandArgument::REQUIRED, "Fichier csv pour l'import"),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'societe';
    $this->name             = 'import-csv';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [importTiers3|INFO] task does things.
Call it with:

  [php symfony importTiers3|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $csv = new SocieteCsvFile($arguments['file']);
        $csv->importSocietes();

    }
}
