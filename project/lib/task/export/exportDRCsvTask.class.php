<?php

class ExportDRCsvTask extends sfBaseTask
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
        ));

        $this->namespace = 'export';
        $this->name = 'dr-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        for($i = 0; $i < 4200; $i++) {

        $dr = DRClient::getInstance()->find($arguments['id'], acCouchdbClient::HYDRATE_JSON);

        $file = sprintf("%s/%s/%s/DR_%s_%s_%s.csv", sfConfig::get('sf_data_dir'), "export/dr/csv", $dr->campagne, $dr->cvi, $dr->campagne, $dr->_rev);

        if(is_file($file)) {

            file_get_contents($file);
            continue;
        }

        continue;

        preg_match("/^DR-([0-9]+)-([0-9]+)$/", $arguments['id'], $matches);

        $campagne = $matches[2];
        $cvi = $matches[1];

        $csvContruct = new ExportDRCsv($matches[2], $matches[1], false);         
        $csvContruct->export();

        $content = $csvContruct->output();

        file_put_contents($file, $content);

        echo $csvContruct->output();
        }
    }
}