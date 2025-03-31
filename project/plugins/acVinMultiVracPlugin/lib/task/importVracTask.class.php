<?php

class importVracTask extends importAbstractTask
{
    protected function configure() {
        // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('file', sfCommandArgument::REQUIRED, "Fichier csv pour l'import"),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'declaration'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'import';
        $this->name = 'vracs';
        $this->briefDescription = 'Importe des contrats depuis un fichier csv';
        $this->detailedDescription = <<<EOF
La tâche [importVrac|INFO] importe des vracs depuis un fichier csv avec un format prédéfini.
Call it with:

  [php symfony importVrac|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $file = new CsvFile($arguments['file']);
        $vracs = VracCsvImport::createFromArray($file->getCsv(), false);
        $vracs->import();

        if ($vracs->getErrors()) {
            foreach ($vracs->getErrors() as $importError) {
                $this->logSection("IMPORT", $importError['message']." at line ".$importError['line'], null, 'ERROR');
            }
        }

        if ($vracs->getWarnings()) {
            foreach ($vracs->getWarnings() as $importWarning) {
                $this->logSection("IMPORT", $importWarning['message']." at line ".$importWarning['line'], null, 'ERROR');
            }
        }

        if (empty($vracs->getErrors())) {
            $imported = $vracs->import(true);
            foreach ($imported as $id) {
                echo "Vrac importé : $id".PHP_EOL;
            }
        }
    }
}
