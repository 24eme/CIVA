<?php

class exportDRUsagesIndustrielsTask extends sfBaseTask
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
    $this->name             = 'dr-usages-industriels';
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
        if($lieu->hasRecapitulatif() && (round($lieu->getUsagesIndustrielsTotal(), 2) != $lieu->getUsagesIndustriels() || round($lieu->getVolumeRevendiqueTotal(), 2) != $lieu->getVolumeRevendique())) {
              echo sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;\n", $dr->campagne, $dr->cvi, $lieu->getHash(), $lieu->getTotalVolume(), $lieu->getVolumeRevendique(), $lieu->getUsagesIndustriels(), $lieu->getDplc(), $lieu->getLies(), $lieu->getLiesMouts());
        }
        foreach($lieu->getCouleurs() as $couleur) {
          if($couleur->hasRecapitulatif() && (round($couleur->getUsagesIndustrielsTotal(), 2) != $couleur->getUsagesIndustriels() || round($couleur->getVolumeRevendiqueTotal(), 2) != $couleur->getVolumeRevendique())) {
            echo sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;\n", $dr->campagne, $dr->cvi, $couleur->getHash(), $couleur->getTotalVolume(), $couleur->getVolumeRevendique(), $couleur->getUsagesIndustriels(), $couleur->getDplc(), $couleur->getLies(), $couleur->getLiesMouts());
          }
          foreach($couleur->getCepages() as $cepage) {
            if(round($cepage->getUsagesIndustrielsTotal(), 2) != $cepage->getUsagesIndustriels() || round($cepage->getVolumeRevendiqueTotal(), 2) != $cepage->getVolumeRevendique()) {
              echo sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n", $dr->campagne, $dr->cvi, $cepage->getHash(), $cepage->getTotalVolume(), $cepage->getVolumeRevendique(), $cepage->getUsagesIndustriels(), $cepage->getDplc(), $cepage->getLies(), $cepage->getLiesMouts(), count($cepage->detail->toArray(true, false)));
            }
          }
        }
      }
    }
  }
}
