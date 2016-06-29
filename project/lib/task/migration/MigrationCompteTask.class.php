<?php

class MigrationCompteTask extends sfBaseTask
{

  protected $_insee = null;

  protected function configure()
  {
     $this->addArguments(array(
       new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'Document ID'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'migration';
    $this->name             = 'compte';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [importTiers3|INFO] task does things.
Call it with:

  [php symfony importTiers3|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $compte = _CompteClient::getInstance()->find($arguments['doc_id'], acCouchdbClient::HYDRATE_JSON);

        if($compte->statut == "INACTIF") {
            return;
        }

        if($compte->type == "CompteProxy") {
            return;
        }

        if($compte->type == "CompteVirtuel") {
            return;
        }

        if(!$compte) {
            echo "Le compte n'a pas été trouvé ".$arguments['doc_id']."\n";
            return;
        }

        $compteMigre = CompteClient::getInstance()->findByLogin($compte->login);

        if(!$compteMigre) {
            echo "La société pour ce compte n'a pas été trouvé ".$compte->_id."\n";
            return;
        }

        $compteMigre->setMotDePasse($compte->mot_de_passe);

  }

}
