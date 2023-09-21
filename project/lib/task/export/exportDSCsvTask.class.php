<?php

class ExportDSCsvTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'ID Document'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('noheader', null, sfCommandOption::PARAMETER_REQUIRED, "N'affiche pas les header", true),
            new sfCommandOption('onlyheader', null, sfCommandOption::PARAMETER_REQUIRED, 'Affiche seulement les headers', false),
        ));

        $this->namespace = 'export';
        $this->name = 'ds-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        if(!$options['noheader'] || $options['onlyheader']) {
            echo ExportDSCsv::getHeader();
        }

        if($options['onlyheader']) {
            return;
        }

        preg_match('/^DS-(C?[0-9]{10})-([0-9]{6})-([0-9]{3})$/', $arguments["id"], $matches);

        $export = new ExportDSCsv($matches[1], $matches[2]);

        echo $export->output();
    }
}
