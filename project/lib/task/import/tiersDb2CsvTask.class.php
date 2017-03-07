<?php

class tiersDb2CsvTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addArguments(array(
           new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'My import from file'),
         ));

        $this->addOptions(array(
          new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
          new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
          new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace        = 'tiers';
        $this->name             = 'db2-csv';
        $this->briefDescription = '';
        $this->detailedDescription = '';
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $export = new Db2Tiers2Csv($arguments['file']);
        $export->printCsv();
    }
}
