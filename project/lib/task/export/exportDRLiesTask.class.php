<?php

class exportDRLiesTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'ID du document'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      
    ));

    $this->namespace        = 'export';
    $this->name             = 'dr-lies';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportDRXml|INFO] task does things.
Call it with:

  [php symfony exportDRXml|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $dr = acCouchdbManager::getClient("DR")->find($arguments['doc_id']);

    if(!$dr->exist('recolte/certification/genre')) {
      return;
    }

    foreach($dr->recolte->certification->genre->getAppellations() as $appellation) {
      foreach($appellation->getLieux() as $lieu) {
        if($lieu->hasRecapitulatif() && $lieu->getLies() > 0 && !$lieu->isLiesSaisisCepage()) {
              echo sprintf("%s;%s;%s;%s\n", $dr->campagne, $dr->cvi, $lieu->getHash(), $lieu->getLies());
        }
        foreach($lieu->getCouleurs() as $couleur) {
          if($couleur->hasRecapitulatif() && $couleur->getLies() > 0 && !$couleur->isLiesSaisisCepage()) {
            echo sprintf("%s;%s;%s;%s\n", $dr->campagne, $dr->cvi, $couleur->getHash(), $couleur->getLies());
          }
          foreach($couleur->getCepages() as $cepage) {
            if($cepage->getLies() > 0 && $cepage->isLiesSaisisCepage()) {
              echo sprintf("%s;%s;%s;%s\n", $dr->campagne, $dr->cvi, $cepage->getHash(), $cepage->getLies());
            }
          }
        }
      }
    }
  }
}
