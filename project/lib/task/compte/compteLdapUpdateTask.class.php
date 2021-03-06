<?php

class compteLdapUpdateTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'compteciva';
    $this->name             = 'ldap-update';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [tiers:ldap-update|INFO] task does things.
Call it with:

  [php symfony tiers:ldap-update|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '512M');

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $ids = acCouchdbManager::getClient('_Compte')->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

    foreach($ids as $id) {
        $compte = sfCouchdbManager::getClient('_Compte')->retrieveDocumentById($id);
        echo $compte->_id."\n";
        $compte->updateLdap();
    }
  }
}
