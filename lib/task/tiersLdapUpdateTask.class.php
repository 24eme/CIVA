<?php

class tiersLdapUpdateTask extends sfBaseTask
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

    $this->namespace        = 'tiers';
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

    $ids_cvi = sfCouchdbManager::getClient('Tiers')->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    $ids_civaba = sfCouchdbManager::getClient('Tiers')->getAllCivaba(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    $ids = array_merge($ids_cvi, $ids_civaba);

    $nb = 0;
    foreach($ids as $id) {
        $tiers = sfCouchdbManager::getClient('Tiers')->retrieveDocumentById($id);
        $ldap = new ldap();
        if($ldap->ldapVerifieExistence($tiers)) {
            $ldap->ldapModify($tiers);
            $nb++;
        }
    }

    $this->logSection("done", $nb);

    // add your code here
  }
}
