<?php

class maintenanceDRDeclarantNomTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
          new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'ID du document'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'dr-declarant-nom';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $dr = acCouchdbManager::getClient()->find($arguments['id']);

        if($dr->type != "DR") {
          return;
        }

        $nom = $dr->declarant->nom;
        $dr->storeDeclarant();
        if($nom != $dr->declarant->nom) {
            echo $dr->_id . ";" . (int)$dr->isValideeCiva() . "\n"; 
            $dr->save();
        }
        
    }

}
