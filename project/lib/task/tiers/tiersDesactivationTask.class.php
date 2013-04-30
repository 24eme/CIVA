<?php

class tiersDesactivationTask extends sfBaseTask
{
    
  protected $_insee = null;

  protected function configure()
  {
     $this->addArguments(array(
       new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'My import from file'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'tiers';
    $this->name             = 'desactivation';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [tiersDesactivation|INFO] task does things.
Call it with:

  [php symfony tiersDesactivation|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    foreach (file($arguments['file']) as $a) {
        $db2_tiers = new Db2Tiers(explode(',', preg_replace('/"/', '', preg_replace('/[^"]+$/', '', $a))));
        $tiers = $this->getTiers($db2_tiers);

        if($tiers) {

          $this->desactiveTiers($tiers);
        }

      
  
        if($db2_tiers->isMetteurEnMarche() && $db2_tiers->get(Db2Tiers::COL_CVI)) {
          $acheteur = sfCouchdbManager::getClient('Acheteur')->retrieveByCvi($db2_tiers->get(Db2Tiers::COL_CVI));
          if($acheteur) {
            $this->desactiveTiers($acheteur);
          }          
        }        

    }
  }

  protected function desactiveTiers($tiers) {
      if($tiers && $tiers->isActif()) {
          $tiers->statut = _TiersClient::STATUT_INACTIF;
          $tiers->save();

          echo sprintf("Le tiers %s;%s a été désactivé\n", $tiers->_id, $tiers->nom);
      }
  }

  protected function getTiers($db2) {
      $tiers = null;
      if ($db2->isRecoltant()) {
          $tiers = $this->getRecoltant($db2);
      } elseif($db2->isMetteurEnMarche()) {
          $tiers = $this->getMetteurEnMarche($db2);
      }
      
      return $tiers;
  }
  
  protected function getRecoltant($db2) {
      $recoltant = sfCouchdbManager::getClient('Recoltant')->retrieveByCvi($db2->get(Db2Tiers::COL_CVI));
      
      return $recoltant;
  }
  
  protected function getMetteurEnMarche($db2) {
      $metteur = sfCouchdbManager::getClient('MetteurEnMarche')->retrieveByCvi($db2->get(Db2Tiers::COL_CIVABA));
      
      return $metteur;
  }
}
