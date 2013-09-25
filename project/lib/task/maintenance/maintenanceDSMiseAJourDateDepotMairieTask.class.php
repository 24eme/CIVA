<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class maintenanceDSMiseAJourDateDepotMairieTask
 * @author mathurin
 */
class maintenanceDSMiseAJourDateDepotMairieTask extends importAbstractTask 
{
    const CSV_CVI = 0;
    const CSV_NOUVELLE_DATE_DEPOT = 1;
    protected $error_term = "\033[31mERREUR:\033[0m";
    
  protected function configure()
  {
    $this->addArguments(array(
            new sfCommandArgument('file', sfCommandArgument::REQUIRED, "Fichier csv"),
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, "Campagne"),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'ds-mise-a-jour-date-depot-mairie';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenance:DSDEPOTENMAIRIE|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '512M');
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    set_time_limit(0);
    $file = file($arguments['file']);
    $campagne = $arguments['campagne'];
    echo "\n*** Application des correctifs pour la date de dépot en mairie ***";
    $this->majDateDepotEnMairie($file,$campagne);
    echo "\n*** FIN DES CORRECTIFS ***\n";
    
  }
  
  protected function majDateDepotEnMairie($file,$campagne) {
      foreach ($file as $line) {
            $datas = str_getcsv($line, ',');
            $cvi = $datas[self::CSV_CVI];
            //echo "\n ** traitement $cvi campagne $campagne";
            $date_depot_mairie_iso = preg_replace('/^(\d+)\/(\d+)\/(\d+)$/', '\3-\2-\1',$datas[self::CSV_NOUVELLE_DATE_DEPOT]);
            $dss = DSCivaClient::getInstance()->findDssByCviAndPeriode($cvi,$campagne.'07');
            if(!count($dss)){
                echo "\n".$this->error_term." Aucune DS n'a été trouvée pour le cvi $cvi sur la campagne $campagne";
                continue;
            }
            $ds_principale = DSCivaClient::getInstance()->getDSPrincipaleByDs(array_pop($dss));
            if(!$ds_principale){
                echo "\n".$this->error_term." La DS principale de la ds $ds_principale->_id n'a pas été trouvée";
                continue;
            }
            if(!$ds_principale->exist('date_depot_mairie')){
                echo "\n".$this->error_term." La DS principale $ds_principale->_id ne possède pas de DDDM?";
            }
            $old_date = $ds_principale->_get('date_depot_mairie');
            echo "\n".$this->green("Correction de la DS ".$ds_principale->_id.": remplacement de la DDDM ".$old_date." par ".$date_depot_mairie_iso);
            $ds_principale->add('date_depot_mairie',$date_depot_mairie_iso);
            $ds_principale->save();
      }
  }
}
