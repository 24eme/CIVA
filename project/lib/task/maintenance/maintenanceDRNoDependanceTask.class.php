<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class maintenanceDRNoDependance
 * @author mathurin
 */

class maintenanceDRNoDependanceTask extends importAbstractTask
{
  protected $error_term = "\033[31mERREUR:\033[0m";
  protected $warning_term = "\033[33m----->ATTENTION:\033[0m ";

  protected function configure()
  {

      $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'campagne')
        ));
      
      $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default')
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'dr-suppression-dependances';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenance:DR-suppression-dependances|INFO] supprimme les dépendances des DR vis à vis des Tiers.
Call it with:

  [php symfony maintenance:dr-suppression-dependances|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '1024M');
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    $campagne = $arguments['campagne'];
    $dr_ids = acCouchdbManager::getClient("DR")->getAllByCampagne($campagne, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    foreach ($dr_ids as $id) {
            $dr = null;
            try {                
                $dr = $this->suppressDependancies($id);
            } catch (sfException $exc) {
                echo $this->error_term." ".$exc->getMessage()."\n";
                continue;
            }

            if(!$dr){
                echo $this->yellow("La DR $id n'a pu être modifiée.")."\n";
                continue;
            }  
           $dr->save();
           echo $this->green("La DR $id a été sauvegardée.")."\n";            
    }
  }
  
  protected function suppressDependancies($id){
      //$dr_json = acCouchdbManager::getClient()->find($id,acCouchdbClient::HYDRATE_);
      $dr = acCouchdbManager::getClient()->find($id);
      if(!$dr){
         echo $this->error_term." la DR $id n'a pas été trouvée en base. \n";
         return null;
      }
//      var_dump(get_class($dr->declarant)); exit;
//      if($dr->exist('declarant') 
//              && $dr->declarant->exist('cvi') 
//              && !is_null($dr->declarant->cvi) 
//              && $dr->declarant->exist('exploitant')
//              && !is_null($dr->declarant->exploitant)){
//          $cvi = $dr->_get('declarant')->cvi;
//          echo $this->warning_term." la DR $id possède déjà un déclarant dont le cvi est $cvi. \n";
//          return null;
//      }
      
      $dr->setDeclarantForUpdate();
      $dr->storeDeclarant();     
      
      return $dr; 
  }
}
