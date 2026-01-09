<?php

class SVExportJsonTask extends sfBaseTask
{
    public function configure()
    {
        $this->addArguments([
            new sfCommandArgument('declaration', sfCommandArgument::REQUIRED, "Type de Document de production"),
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, "Campagne"),
            new sfCommandArgument('id', sfCommandArgument::OPTIONAL, "SV spécifique")
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

        $filtre = $arguments['id'] ?? null;

        $clientClassName = "SVClient";
        if($declaration == "DR") {
            $clientClassName = "DRClient";
        }
        $svs = [];
        if (!$filtre) {
            $allSV = $clientClassName::getInstance()->getAllByCampagne($campagne);
            foreach ($allSV as $sv) {
                if ($sv->type !== $declaration) {
                    continue;
                }

                if (strpos($sv->_id, '-75') !== false) {
                    continue;
                }

                if ($filtre && $filtre !== $sv->_id) {
                    continue;
                }

                $svs[] = $sv;
            }
        } else {
            $svs[] = $clientClassName::getInstance()->find($filtre);
        }

        $class = "Export".$declaration."Json";
        $json = [$class::ROOT_NODE => []];

        foreach ($svs as $sv) {
            if ($sv->exist('apporteurs') && empty($sv->apporteurs->toArray())) {
                continue;
            }

            $export = new $class($sv);
            $export->build();

            $exportRaw = $export->export();
            if(!$exportRaw) {
                continue;
            }
            $json[$class::ROOT_NODE][] = json_decode($exportRaw);
        }

        echo json_encode($json).PHP_EOL;
    }
}
