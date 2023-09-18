<?php

class SVExportMouvementsToCSVTask extends sfBaseTask
{
    public function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Année de déclaration'),
            new sfCommandArgument('cvi', sfCommandArgument::OPTIONAL, 'CVI de l\'opérateur'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
            //new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, "Année de déclaration", '2010'),
        ));

        $this->namespace        = 'sv';
        $this->name             = 'export-mouvements-csv';
        $this->briefDescription = 'Export des SV au format CSV';
        $this->detailedDescription = <<<EOF
[ExportToCSV|INFO] exporte les SV11 et SV12 au format csv, une ligne par produit.
  [php symfony ExportToCSV [cvi] [campagne]|INFO]
EOF;
    }

    public function execute($arguments = [], $options = [])
    {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $campagne = $arguments['campagne'];
        $cvi = (isset($arguments['cvi'])) ? $arguments['cvi'] : null;

        $export = new ExportSVMouvementsCsv($cvi);
        $export->generate($campagne);
    }
}
