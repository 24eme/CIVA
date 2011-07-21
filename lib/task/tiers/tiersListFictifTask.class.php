<?php

class tiersListFictifTask extends sfBaseTask
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
    $this->name             = 'list-fictif';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [tiersListFictif|INFO] task does things.
Call it with:

  [php symfony tiersListFictif|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $ldap = new ldap();
    $entries = $ldap->getEntriesByGroupe('exterieur');

    foreach($entries as $item) {
        $this->logSection($item['uid'][0], $item['gecos'][0]);
    }
    // add your code here
  }
}
