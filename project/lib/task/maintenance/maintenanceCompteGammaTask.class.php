<?php

class maintenanceCompteGammaTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
          new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'Campagne'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'compte-gamma';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $compte = CompteClient::getInstance()->find($arguments['doc_id']);

        if(!$compte) {
            echo "Le compte ".$arguments['doc_id']." n'existe pas\n";
            return;
        }

        if($compte->exist('droits') && in_array("gamma", $compte->droits->toArray())) {
            return;
        }

        $compte->add('droits')->add(null, 'gamma');
        $compte->save();
    }


}
