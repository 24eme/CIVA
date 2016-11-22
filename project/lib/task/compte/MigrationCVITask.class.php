<?php

class MigrationCVITask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(
            new sfCommandArgument('ancien_cvi', sfCommandArgument::REQUIRED, 'Ancien cvi'),
            new sfCommandArgument('nouveau_cvi', sfCommandArgument::REQUIRED, 'Nouveau cvi'),
            new sfCommandArgument('keep_password', sfCommandArgument::OPTIONAL, 'Conserver le mpt de passe', true),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'migration';
        $this->name = 'cvi';
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

        $migration = new MigrationCVI($arguments['ancien_cvi'], $arguments['nouveau_cvi'], $arguments['keep_password']);
        $migration->process();
    }

}
