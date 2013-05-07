<?php

class maintenanceMigrateDrTask extends sfBaseTask
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

    $this->namespace        = 'maintenance';
    $this->name             = 'migrateDr';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '128M');
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $c = acCouchdbManager::getClient();

    foreach ($c->getAllDocs()->rows as $id => $doc) {
      if (!preg_match('/^(DR-|CONFIGURATION)/', $doc->id)) {
	continue;
      }
      $id = $doc->id;
      $dr = $c->getDoc($id);
      print "id: ".$dr->_id."\n";
      foreach ($dr->recolte as $k => $a) {
	foreach ($a as $kl => $l) {
	  if (!preg_match('/^lieu/', $kl))
	    continue;
	  if (isset($a->{$kl}->couleur))
	    continue;
	  $a->{$kl}->couleur = new stdClass();
	  foreach ($l as $kc => $ce) {
	    if (!preg_match('/^cepage/', $kc)) continue;
	    $a->{$kl}->couleur->{$kc} = new stdClass();
	    unset($a->{$kl}->{$kc});
	    $a->{$kl}->couleur->{$kc} = $ce;
	  }
	}
      }
      $c->storeDoc($dr);
    }
  }
}
