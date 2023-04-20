<?php

class SVExportJsonTask extends sfBaseTask
{
    public function configure()
    {
        $this->addArguments([
            new sfCommandArgument('declaration', sfCommandArgument::REQUIRED, "Type de Document de production"),
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, "Campagne")
        ]);

        $this->addOptions([
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ]);

        $this->namespace        = 'sv';
        $this->name             = 'export-json';
        $this->briefDescription = 'Export des SV au format json';
        $this->detailedDescription = <<<EOF
[ExportToCSV|INFO] exporte les SV11 et SV12 au format json, selon le format des douanes.
  [php symfony ExportToCSV [doc]|INFO]
    }
EOF;
    }

    public function execute($arguments = [], $options = [])
    {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $declaration = $arguments['declaration'];
        $campagne = $arguments['campagne'];

        $svs = [];
        $allSV = SVClient::getInstance()->getAll($campagne);
        foreach ($allSV as $sv) {
            if ($sv->type !== $declaration) {
                continue;
            }

            if (strpos($sv->_id, '-75') !== false) {
                continue;
            }

            $svs[] = $sv;
        }

        $class = "Export".$declaration."Json";
        $json = [$class::ROOT_NODE => []];

        foreach ($svs as $sv) {
            if (empty($sv->apporteurs->toArray())) {
                continue;
            }

            $export = new $class($sv);
            $export->build();
            $json[$class::ROOT_NODE][] = json_decode($export->export());
        }

        echo json_encode($json).PHP_EOL;
    }
}
