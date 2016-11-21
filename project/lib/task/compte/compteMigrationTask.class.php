<?php

class compteMigrationTask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(
                new sfCommandArgument('cvi', sfCommandArgument::REQUIRED, 'cvi'),
                new sfCommandArgument('nouveau_cvi', sfCommandArgument::REQUIRED, 'Nouveau cvi'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('withCopyPasswords', null, sfCommandOption::PARAMETER_OPTIONAL, 'With Update of password', '0'),
             new sfCommandOption('password', null, sfCommandOption::PARAMETER_OPTIONAL, 'password', null),
        ));

        $this->namespace = 'compte';
        $this->name = 'migration';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [importCompte|INFO] task does things.
Call it with:

  [php symfony importCompte|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $withCopyPasswords = (isset($options['withCopyPasswords']))? $options['withCopyPasswords'] : false;
        if(!preg_match('/[0-9]{10}/', $arguments['nouveau_cvi'])) {

            throw new sfCommandException("Le cvi semble invalide");
        }

        if (acCouchdbManager::getClient('_Compte')->retrieveByLogin($arguments['nouveau_cvi'])) {

            throw new sfCommandException("Le nouveau compte existe déjà");
        }

        $migration = new MigrationCompte($arguments['cvi'], $arguments['nouveau_cvi'], $withCopyPasswords);
        $migration->process();
    }

}
