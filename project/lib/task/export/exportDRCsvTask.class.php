<?php

class ExportDRCsvTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('ids', sfCommandArgument::IS_ARRAY, 'ID Document'),
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

        foreach(file("php://stdin") as $id) {
            $arguments['ids'][] = trim($id);
        }

        foreach ($arguments['ids'] as $id) {
            $dr = DRClient::getInstance()->find($id, acCouchdbClient::HYDRATE_JSON);

            if(!$dr) {

                throw new sfCommandArgumentsException("DR non trouvé : '".$id."'");
            }

            $csvPath = sfConfig::get('sf_data_dir')."/export/dr/csv/".$dr->campagne;
            if(!is_dir($csvPath)) {
                mkdir($csvPath);
            }

            $file = sprintf("%s/DR_%s_%s_%s.csv", $csvPath, $dr->cvi, $dr->campagne, $dr->_rev);

            if(is_file($file)) {

                echo file_get_contents($file);
                continue;
            }

            $csvContruct = new ExportDRCsv($dr->campagne, $dr->cvi, false);         
            $csvContruct->export();

            $content = $csvContruct->output();

            file_put_contents($file, $content);

            echo $csvContruct->output();
        }
    }
}