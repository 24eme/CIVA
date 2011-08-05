<?php

class exportDRAcheteurCsvTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
     $this->addArguments(array(
       new sfCommandArgument('cvi', sfCommandArgument::REQUIRED, "Numéro cvi de l'acheteur"),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'export';
    $this->name             = 'dr-acheteur-csv';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportDRAcheteursCsv|INFO] task does things.
Call it with:

  [php symfony exportDRAcheteursCsv|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    
    $export = new ExportDRAcheteurCsv('2010', $arguments['cvi'], true);
  }
}
