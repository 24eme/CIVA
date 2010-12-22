<?php

class tiersFixUpdateRevisionTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'tiers';
    $this->name             = 'fix-update-revision';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [tiers:fix-update-revision|INFO] task does things.
Call it with:

  [php symfony tiers:fix-update-revision|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '512M');

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $ids = sfCouchdbManager::getClient('Tiers')->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    $nb = 0;
    foreach($ids as $id) {
        $tiers = sfCouchdbManager::getClient('Tiers')->retrieveDocumentById($id);
        if ($tiers->recoltant == 1) {
            $tiers->add('revision_dernieres_modifications', $tiers->get('_rev'));
            $tiers->save();
            $nb++;
        }
    }
    $this->logSection("done", $nb);
    // add your code here
  }
}
