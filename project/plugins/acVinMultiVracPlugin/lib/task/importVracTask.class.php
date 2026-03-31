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
        sfContext::createInstance($this->configuration);
        $this->vracimport = new VracCsvImport($arguments['file'], null);
        $this->vracimport->preimportChecks();
        $this->vracimport->import(false, true);

        if ($this->vracimport->getErrors()) {
            foreach ($this->vracimport->getErrors() as $importError) {
                $this->logSection("ERROR", $importError->erreur_csv." at line ".$importError->num_ligne.";".$importError->raison.";".implode(",",$importError->ligne), -1, 'ERROR');
            }
        }

        if ($this->vracimport->getWarnings()) {
            foreach ($vracs->getWarnings() as $importWarning) {
                $this->logSection("WARNING", $importError->erreur_csv." at line ".$importError->num_ligne.";".$importError->raison, -1, 'INFO');
            }
        }

        $imported = $this->vracimport->import(true, true);
        foreach ($imported as $id) {
            echo "Vrac importé : $id".PHP_EOL;
        }
    }
}
