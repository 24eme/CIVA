<?php

class maintenanceCompteUpdateTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
          new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'Campagne'),
          new sfCommandArgument('mot_de_passe', sfCommandArgument::REQUIRED, 'mot de passe'),
          new sfCommandArgument('email', sfCommandArgument::OPTIONAL, 'email'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'compte-update';
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

        $master = $compte->getMasterObject();
        $master->setEmail($arguments['email']);
        $master->save();

        $compte = CompteClient::getInstance()->find($arguments['doc_id']);
        $compte->setMotDePasse($arguments['mot_de_passe']);
        $compte->save();
    }


}
