<?php

class SVExportJsonTask extends sfBaseTask
{
    public function configure()
    {
        $this->addArguments([
            new sfCommandArgument('declaration', sfCommandArgument::REQUIRED, "Document de production")
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

        $sv = SVClient::getInstance()->find($declaration);

        if ($sv) {
            $class = "Export".$sv->getType()."Json";

            $export = new $class($sv);
            $export->build();
            echo $export->export();
        }
    }
}
