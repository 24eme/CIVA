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
        $tiers = $this->loadAndSaveTiers($db2_tiers);

        if($db2_tiers->isAcheteur()) {
          $tiers = $this->loadAndSaveTiers($db2_tiers, true);
        }
    }
  }

  protected function loadAndSaveTiers($db2, $acheteur = false) {
    $tiers = $this->loadTiers($db2, $acheteur);
    if(!$tiers) {

      return null;
    }

    if($tiers->isNew()) {
       echo "INFO;CREATION;".$tiers->get('_id')."\n";
    } elseif($tiers->isModified()) {
       echo "INFO;MODIFICATION;".$tiers->get('_id')."\n";
    }
    if($tiers->save()) {
      $tiers->db2->add('import_revision', $tiers->_rev);
      $tiers->db2->import_date = date("Y-m-d");
      $tiers->save();
    }

    return $tiers;
  }
  
  /**
   * @param Db2Tiers $db2 
   * return _Tiers
   */
  private function loadTiers($db2, $acheteur = false) {
      $tiers = null;
      if($acheteur && $db2->isAcheteur()) {
        $tiers = $this->loadAcheteur($db2);
      }

      if (!$acheteur && $db2->isRecoltant()) {
          $tiers = $this->loadRecoltant($db2);
      } elseif(!$acheteur && $db2->isMetteurEnMarche()) {
          $tiers = $this->loadMetteurEnMarche($db2);
      } elseif(!$acheteur && $db2->isCourtier()) {
          $tiers = $this->loadCourtier($db2);
      }
      
      if(!$tiers) {
          return null;
      }

      $tiers->statut = _TiersClient::STATUT_ACTIF;
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
      if($db2->get(Db2Tiers::COL_TYPE_TIERS) == "CCV" && $db2->get(Db2Tiers::COL_CCV_REC) == "S") {
        $tiers->categorie = "CCV_REC";
      } else {
        $tiers->categorie = $db2->get(Db2Tiers::COL_TYPE_TIERS);
      }
      $tiers->qualite_categorie = $this->getQualite($db2);
      $tiers->db2->num = $db2->get(Db2Tiers::COL_NUM);
      $tiers->db2->no_stock = $db2->get(Db2Tiers::COL_NO_STOCK);
      $tiers->db2->remove('export_revision');

      return $tiers;
  }
  
  /**
   * @param Db2Tiers $db2 
   * return Recoltant
   */
  private function loadRecoltant($db2) {
      $recoltant = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($db2->get(Db2Tiers::COL_CVI));
      
      if(!$recoltant) {
          $recoltant = new Recoltant();
          $recoltant->set('_id', "REC-" . $db2->get(Db2Tiers::COL_CVI));
      }
      
      $recoltant->civaba = ($db2->get(Db2Tiers::COL_CIVABA)) ? $db2->get(Db2Tiers::COL_CIVABA) : null;
      $recoltant->cvi = $db2->get(Db2Tiers::COL_CVI);
      $recoltant->siret = $db2->get(Db2Tiers::COL_SIRET);
      $recoltant->cave_cooperative = $this->getCaveParticuliere($db2->get(Db2Tiers::COL_TYPE_DECLARATION));
      $recoltant->declaration_insee = $db2->get(Db2Tiers::COL_INSEE_DECLARATION);
      $recoltant->declaration_commune = $this->getCommune($db2->get(Db2Tiers::COL_INSEE_DECLARATION));

      return $recoltant;
  }
  
  /**
   * @param Db2Tiers $db2 
   * return MetteurEnMarche
   */
  private function loadMetteurEnMarche($db2) {
      $metteur = acCouchdbManager::getClient('MetteurEnMarche')->retrieveByCvi($db2->get(Db2Tiers::COL_CIVABA));
      
      if(!$metteur) {
          $metteur = new MetteurEnMarche();
          $metteur->set('_id', "MET-" . $db2->get(Db2Tiers::COL_CIVABA));
      }
      
      $metteur->remove('cvi_acheteur');
      $metteur->cvi = ($db2->get(Db2Tiers::COL_CVI)) ? $db2->get(Db2Tiers::COL_CVI) : null;
      $metteur->civaba = $db2->get(Db2Tiers::COL_CIVABA);
      $metteur->no_accises = $db2->get(Db2Tiers::COL_NO_ASSICES);
      $metteur->siret = $db2->get(Db2Tiers::COL_SIRET);

      return $metteur;
  }
  
  /**
   * @param Db2Tiers $db2 
   * return MetteurEnMarche
   */
  private function loadCourtier($db2) {
  		$siren = ($db2->get(Db2Tiers::COL_SIRET)) ? substr($db2->get(Db2Tiers::COL_SIRET), 0, 9) : null;
  		if ($siren) {
  			$courtier = acCouchdbManager::getClient('Courtier')->retrieveBySiren($siren);
	      	if(!$courtier) {
	        	$courtier = new Courtier();
	          	$courtier->set('_id', "COURT-" . $siren);
	      	}
	      	$courtier->no_accises = $db2->get(Db2Tiers::COL_NO_ASSICES);
	      	$courtier->siren = ($db2->get(Db2Tiers::COL_SIRET)) ? substr($db2->get(Db2Tiers::COL_SIRET), 0, 9) : null;
	      	$courtier->siret = $db2->get(Db2Tiers::COL_SIRET);
	      	$courtier->no_carte_professionnelle = $db2->get(Db2Tiers::COL_SITE_INTERNET);
	      	return $courtier;
  		}
  		return null;
  }

  private function loadAcheteur($db2) {
      $achat = acCouchdbManager::getClient('Acheteur')->retrieveByCvi($db2->get(Db2Tiers::COL_CVI));

      if(!$achat) {

          return null;
      }
      $achat->civaba = ($db2->get(Db2Tiers::COL_CIVABA)) ? $db2->get(Db2Tiers::COL_CIVABA) : null;
      $achat->cvi = $db2->get(Db2Tiers::COL_CVI);
      $achat->declaration_insee = $db2->get(Db2Tiers::COL_INSEE_DECLARATION);
      $achat->declaration_commune = $this->getCommune($db2->get(Db2Tiers::COL_INSEE_DECLARATION));
      $achat->siret = $db2->get(Db2Tiers::COL_SIRET);
      $achat->no_accises = $db2->get(Db2Tiers::COL_NO_ASSICES);
      
      return $achat;
  }

  private function getQualite($db2) {

    if($db2->isRecoltant()) {

      return _TiersClient::QUALITE_RECOLTANT;
    }

    if($db2->isCourtier()) {

      return _TiersClient::QUALITE_COURTIER;
    }

    if($db2->get(Db2Tiers::COL_TYPE_TIERS) == 'PN' || $db2->get(Db2Tiers::COL_TYPE_TIERS) == 'SIC') {

      return _TiersClient::QUALITE_NEGOCIANT;
    }

    if($db2->get(Db2Tiers::COL_TYPE_TIERS) == 'CCV') {

      return _TiersClient::QUALITE_COOPERATIVE;
    }

    if(preg_match("/^V/", $db2->get(Db2Tiers::COL_TYPE_TIERS))) {

      return _TiersClient::QUALITE_RECOLTANT;
    }

    
    return null;
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
