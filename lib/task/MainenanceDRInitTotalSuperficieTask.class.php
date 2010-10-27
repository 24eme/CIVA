<?php

class MainenanceDRInitTotalSuperficieTask extends sfBaseTask
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

    $this->namespace        = 'maintenance';
    $this->name             = 'dr-init-total-superficie';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [MainenanceDRTotalSuperficie|INFO] task does things.
Call it with:

  [php symfony MainenanceDRTotalSuperficie|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $docs = sfCouchdbManager::getClient('DR')->getAllByCampagne('2010');
    $i = 0;
    $keys = array_keys($docs->getDocs());
    foreach($keys as $key) {
        //echo substr($key, strlen($key) - 4, 4);
        if (substr($key, strlen($key) - 4, 4) == "2010") {
            $this->log($key);
            foreach($docs[$key]->recolte->getAppellations() as $appellation) {
              $appellation->total_superficie = null;
              $this->log($appellation->getKey());
            }
            $docs[$key]->save();
            $i++;
        }
    }

    $this->log($i);

    // add your code here
  }
}
