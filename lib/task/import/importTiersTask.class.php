<?php

class importTiersTask extends sfBaseTask
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

    $this->namespace        = 'import';
    $this->name             = 'Tiers';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [importTiers3|INFO] task does things.
Call it with:

  [php symfony importTiers3|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    
    $nb_not_use = 0;
    
    foreach (file($arguments['file']) as $a) {
        //$db2_tiers = new Db2Tiers(explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $a))));
        $db2_tiers = new Db2Tiers(explode(',', preg_replace('/"/', '', preg_replace('/[^"]+$/', '', $a))));
        $tiers = $this->loadTiers($db2_tiers);
        if ($tiers) {
            $this->log($tiers->get('_id'));
            $tiers->save(); 
        } else {
            $nb_not_use++;
        }
    }
    
    $this->logSection("nb not use", $nb_not_use);

    // add your code here
  }
  
  /**
   * @param Db2Tiers $db2 
   * return _Tiers
   */
  private function loadTiers($db2) {
      $tiers = null;
      if ($db2->isRecoltant()) {
          $tiers = $this->loadRecoltant($db2);
      } elseif($db2->isMetteurEnMarche()) {
          $tiers = $this->loadMetteurEnMarche($db2);
      }
      
      if(!$tiers) {
          return null;
      }
      
      $tiers->civaba = $db2->get(Db2Tiers::COL_CIVABA);
      $tiers->intitule = $db2->get(Db2Tiers::COL_INTITULE);
      $tiers->nom = preg_replace('/ +/', ' ', $db2->get(Db2Tiers::COL_NOM_PRENOM));
      $tiers->telephone = $db2->get(Db2Tiers::COL_TELEPHONE_PRO) ? sprintf('%010d', $db2->get(Db2Tiers::COL_TELEPHONE_PRO)) : null;
      $tiers->fax = $db2->get(Db2Tiers::COL_FAX) ? sprintf('%010d', $db2->get(Db2Tiers::COL_FAX)) : null;
      $tiers->email = $db2->get(Db2Tiers::COL_EMAIL);
      $tiers->web = $db2->get(Db2Tiers::COL_SITE_INTERNET);
      $tiers->add('exploitant');
      $tiers->exploitant->sexe = $db2->get(Db2Tiers::COL_SEXE_CHEF_ENTR);
      $tiers->exploitant->nom = $db2->get(Db2Tiers::COL_NOM_PRENOM_CHEF_ENTR);
      $tiers->exploitant->adresse = $db2->get(Db2Tiers::COL_NUMERO) ? $db2->get(Db2Tiers::COL_NUMERO). ', ' . $db2->get(Db2Tiers::COL_ADRESSE) : $db2->get(Db2Tiers::COL_ADRESSE);
      $tiers->exploitant->code_postal = $db2->get(Db2Tiers::COL_CODE_POSTAL);
      $tiers->exploitant->commune = $db2->get(Db2Tiers::COL_COMMUNE);

      $tiers->exploitant->date_naissance = sprintf("%04d-%02d-%02d", $db2->get(Db2Tiers::COL_ANNEE_NAISSANCE), 
                                                                     $db2->get(Db2Tiers::COL_MOIS_NAISSANCE), 
                                                                     $db2->get(Db2Tiers::COL_JOUR_NAISSANCE));
      
      $tiers->exploitant->telephone = $db2->get(Db2Tiers::COL_TELEPHONE_PRIVE) ? sprintf('%010d', $db2->get(Db2Tiers::COL_TELEPHONE_PRIVE)) : null;
      $tiers->siege->adresse = $db2->get(Db2Tiers::COL_ADRESSE_SIEGE);
      $tiers->siege->insee_commune = $db2->get(Db2Tiers::COL_INSEE_SIEGE);
      $tiers->siege->code_postal = $db2->get(Db2Tiers::COL_CODE_POSTAL_SIEGE);
      $tiers->siege->commune = $db2->get(Db2Tiers::COL_COMMUNE_SIEGE);
      $tiers->categorie = $db2->get(Db2Tiers::COL_TYPE_TIERS);
      $tiers->db2->num = $db2->get(Db2Tiers::COL_NUM);
      $tiers->db2->no_stock = $db2->get(Db2Tiers::COL_NO_STOCK);
      $tiers->db2->import_date = date("Y-m-d");
      $tiers->db2->export_revision = null; 
      
      return $tiers;
  }
  
  /**
   * @param Db2Tiers $db2 
   * return Recoltant
   */
  private function loadRecoltant($db2) {
      $recoltant = sfCouchdbManager::getClient('Recoltant')->retrieveByCvi($db2->get(Db2Tiers::COL_CVI));
      
      if(!$recoltant) {
          $recoltant = new Recoltant();
          $recoltant->set('_id', "REC-" . $db2->get(Db2Tiers::COL_CVI));
      }
      
      $recoltant->cvi = $db2->get(Db2Tiers::COL_CVI);
      $recoltant->declaration_insee = $db2->get(Db2Tiers::COL_INSEE_DECLARATION);
      $recoltant->declaration_commune = $this->getCommune($db2->get(Db2Tiers::COL_INSEE_DECLARATION));
      $recoltant->siret = $db2->get(Db2Tiers::COL_SIRET);
      $recoltant->cave_cooperative = $this->getCaveParticuliere($db2->get(Db2Tiers::COL_TYPE_DECLARATION));
      
      return $recoltant;
  }
  
  /**
   * @param Db2Tiers $db2 
   * return MetteurEnMarche
   */
  private function loadMetteurEnMarche($db2) {
      $metteur = sfCouchdbManager::getClient('MetteurEnMarche')->retrieveByCvi($db2->get(Db2Tiers::COL_CIVABA));
      
      if(!$metteur) {
          $metteur = new MetteurEnMarche();
          $metteur->set('_id', "MET-" . $db2->get(Db2Tiers::COL_CIVABA));
          $metteur->compte = 'COMPTE-';
      }
      
      $metteur->cvi_acheteur = $db2->get(Db2Tiers::COL_CVI);
      $metteur->no_accises = $db2->get(Db2Tiers::COL_NO_ASSICES);
      $metteur->siren = ($db2->get(Db2Tiers::COL_SIRET)) ? substr($db2->get(Db2Tiers::COL_SIRET), 0, 9) : null;
      $metteur->db2->maison_mere = $db2->get(Db2Tiers::COL_MAISON_MERE);
      
      return $metteur;
  }
  
  private function getCommune($insee) {
        if (is_null($this->_insee)) {
            $csv = array();
            $this->_insee = array();
            foreach (file(sfConfig::get('sf_data_dir') . '/import/Commune') as $c) {
                $csv = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
                $this->_insee[$csv[0]] = $csv[1];
            }
        }
        
        if(array_key_exists($insee, $this->_insee)) {
            return $this->_insee[$insee];
        } else {
            return null;
        }
   }
   
   private function getCaveParticuliere($cave_particuliere) {
       $donnees = array(
        "A" => "ANDLAU-BARR",
        "B" => "BEBLENHEIM",
        "C" => "CLEEBOURG",
        "D" => "DAMBACH LA VILLE",
        "E" => "EGUISHEIM",
        "G" => "VENDANG. DANGOLSHEIM",
        "H" => "HUNAWIHR",
        "I" => "INGERSHEIM",
        "K" => "KIENTZH.-KAYSERSBERG",
        "L" => "VENDANG. DORLISHEIM",
        "N" => "BENNWIHR",
        "O" => "ORSCHWILLER-KINTZH.",
        "P" => "PFAFFENHEIM-GUEBERS.",
        "R" => "RIBEAUVILLE",
        "S" => "SIGOLSHEIM",
        "T" => "TRAENHEIM",
        "U" => "TURCKHEIM",
        "V" => "WUENHEIM",
        "W" => "WESTHALTEN",
        "X" => "ST-HIPPOLYTE (KOEN.)",
        "Y" => "ST-HIPPOLYTE (WUEN.)",
        "Z" => "ST-HIPPOLYTE (CIRRA)",
       );
       
       if (array_key_exists($cave_particuliere, $donnees)) {
           return $donnees[$cave_particuliere];
       } else {
           return null;
       }
   }
}
