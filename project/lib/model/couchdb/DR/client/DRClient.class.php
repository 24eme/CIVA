<?php

class DRClient extends acCouchdbClient {
  
  public static function getInstance() {

    return acCouchdbManager::getClient('DR');
  }

  public function createDeclaration($tiers, $campagne, $depot_mairie = false) {
    $doc = new DR();
    $doc->campagne = $campagne;
    $doc->cvi = $tiers->cvi;
    $this->initDeclaration($doc, $depot_mairie);

    return $doc;
  }

  public function createDeclarationClone($dr, $tiers, $campagne, $depot_mairie) {
    $doc = clone $dr;
    $doc->campagne = $campagne;
    $doc->cvi = $tiers->cvi;
    $this->initDeclaration($doc, $depot_mairie);
    $doc->devalidate();
    $doc->removeVolumes();
    $doc->remove('etape');
    $doc->remove('utilisateurs');
    $doc->remove('import_db2');
    $doc->update();

    return $doc;
  }

  protected function initDeclaration($doc, $depot_mairie = false) {
    $doc->constructId();
    $doc->storeDeclarant();
    if($depot_mairie){
      $doc->add('date_depot_mairie', null);                    
    }
    $doc->add('lies_saisis_cepage', 1);
  }

  public function hasImport($cvi, $campagne) {

    return acCouchdbManager::getClient('CSV')->countCSVsFromRecoltant($campagne, $cvi) > 0;
  }

  public function getAcheteursApporteur($cvi, $campagne) {
    $csv_ids = CSVClient::getInstance()->getCSVsFromRecoltantArray($campagne, $cvi);
    $acheteurs = array();
    foreach($csv_ids as $csv_id) {
      $acheteurs[] = acCouchdbManager::getClient()->find(preg_replace("/^CSV-([0-9]+)-.*/", 'ACHAT-\1', $csv_id));
    }

    return $acheteurs;
  }

  public function createFromCSVRecoltant($campagne, $tiers, &$import, $depot_mairie = false) {
    $csvs = acCouchdbManager::getClient('CSV')->getCSVsFromRecoltant($campagne, $tiers->cvi);
    if (!$csvs || !count($csvs))
      throw new sfException('no csv found for '.$tiers->cvi) ;
    $campagne = $csvs[0]->campagne;
    
    $doc = $this->createDeclaration($tiers, $campagne, $depot_mairie);
    $doc->jeunes_vignes = 0;
    foreach ($csvs as $csv) {
          $acheteur_cvi = $csv->cvi;
          $acheteur_obj = acCouchdbManager::getClient('Acheteur')->retrieveByCvi($csv->cvi);

          if (!$acheteur_obj)
        throw new sfException($acheteur_cvi.' acheteur inconnu');

          $import[] = $acheteur_obj;
          $linenum = 0;
          foreach ($csv->getCsvRecoltant($tiers->cvi) as $line) {
        $linenum++;
        if (preg_match('/JEUNES +VIGNES/i', $line[CsvFile::CSV_APPELLATION])) {
          if($doc->jeunes_vignes == $this->recodeNumber($line[CsvFile::CSV_SUPERFICIE])) {
            continue;
          }
          $doc->jeunes_vignes += $this->recodeNumber($line[CsvFile::CSV_SUPERFICIE]);
          continue;
        }
        $prod = ConfigurationClient::getConfiguration()->identifyProduct($line[CsvFile::CSV_APPELLATION],
                                         $line[CsvFile::CSV_LIEU],
                                         $line[CsvFile::CSV_CEPAGE]);


        if (!isset($prod['hash']))
          throw new sfException("Error on ".$prod['error']." (line $linenum / acheteur = $acheteur_cvi / recoltant = ".$tiers->cvi.')');

        $cepage = $doc->getOrAdd($prod['hash']);

        $denomlieu = '';
        if ($cepage->getLieu()->getKey() == 'lieu')
          $denomlieu = $line[CsvFile::CSV_LIEU];
        $detail = $cepage->retrieveDetailFromUniqueKeyOrCreateIt($line[CsvFile::CSV_DENOMINATION], $line[CsvFile::CSV_VTSGN], $denomlieu);
        $detail->superficie += $this->recodeNumber($line[CsvFile::CSV_SUPERFICIE]);
        $detail->volume += $this->recodeNumber($line[CsvFile::CSV_VOLUME]);
        if ($this->recodeNumber($line[CsvFile::CSV_VOLUME]) == 0) {
          $detail->denomination = 'repli';
          $detail->add('motif_non_recolte', 'AE');
        }
          if($this->recodeNumber($line[CsvFile::CSV_VOLUME]) > 0 || $this->recodeNumber($line[CsvFile::CSV_SUPERFICIE]) > 0)
          {
            $acheteurs = $detail->add($acheteur_obj->getAcheteurDRType());
            $acheteur = null;
            foreach ($acheteurs as $a) {
              if ($a->cvi == $acheteur_cvi)
                  $acheteur = $a;
                  break;
            }
            if (!$acheteur)
              $acheteur = $acheteurs->add();
            $acheteur->cvi = $acheteur_cvi;
            $acheteur->quantite_vendue += $this->recodeNumber($line[CsvFile::CSV_VOLUME]);
          }
        }
    }
    $doc->utilisateurs->edition->add('csv', date('d/m/Y'));
    $doc->update();
    return $doc;
  }

  protected function recodeNumber($value) {

    return round(str_replace(",", ".", $value)*1, 2);
  }

    public function retrieveByCampagneAndCvi($cvi, $campagne, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

      return $this->find('DR-'.$cvi.'-'.$campagne, $hydrate);
    }

    public function getAllByCampagne($campagne, $hydrate = acCouchdbClient::HYDRATE_ON_DEMAND) {
        $docs = $this->getAll($hydrate);
        $i = 0;
        $keys = array_keys($docs->getDocs());
        foreach($keys as $key) {
            //echo substr($key, strlen($key) - 4, 4);
            if (substr($key, strlen($key) - 4, 4) != $campagne) {
                unset($docs[$key]);
            }
        }
        return $docs;
    }

    public function getAllByCvi($cvi, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
       return $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-2020')->execute($hydrate);
    }

    /*
     *
     * @param string $cvi
     * @param string $campagne
     * @return array 
     */
    public function getArchivesSince($cvi, $campagne, $limit) {
        $docs = $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-'.$campagne)->execute(acCouchdbClient::HYDRATE_ON_DEMAND);
        $campagnes = array();
        foreach($docs->getIds() as $doc_id) {
            preg_match('/DR-(?P<cvi>\d+)-(?P<campagne>\d+)/', $doc_id, $matches);
            $campagnes[$doc_id] = $matches['campagne'];
        }
        krsort($campagnes);

        return array_slice($campagnes, 0, $limit);
    }

    public function getAll($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return $this->startkey('DR-0000000000-0000')->endkey('DR-9999999999-9999')->execute($hydrate);
    }
    
    public function findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        
        return $this->startkey(array($campagne, $cvi_acheteur))->endkey(array($campagne, (string)($cvi_acheteur + 1)))->executeView("DR", "campagne_acheteur", $hydrate);
    }
    
    public function findAllByCampagneAcheteurs($campagne, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        
        return $this->startkey(array((string)$campagne))->endkey(array((string)($campagne+1)))->executeView("DR", "campagne_acheteur", $hydrate);
    }

    public function getTotauxByAppellationsRecap($dr) {
        $totauxByAppellationsRecap = array();

        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEBLANC', null, "AOC Alsace Blanc");
        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEROUGEROSE', null, "Rouge ou Rosé");
        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'GRDCRU', null, "AOC Alsace Grands Crus");
        $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'CREMANT', null, "AOC Crémant d'Alsace");

          foreach ($dr->recolte->getAppellationsSorted() as $app_key => $appellation) {
              switch ($appellation_key = preg_replace('/^appellation_/', '', $app_key)) {
                  case 'GRDCRU':
                  case 'CREMANT':
                      $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, $appellation_key, $appellation, $appellation->getLibelle());
                      break;
                  case 'ALSACEBLANC':
                  case 'COMMUNALE':
                  case 'LIEUDIT':
                  case 'PINOTNOIRROUGE':
                  case 'PINOTNOIR':
                      $totauxByAppellationsRecap = $this->getTotauxAgregeByCouleur($totauxByAppellationsRecap, $appellation_key, $appellation);
                      break;
              }
          }
        return $totauxByAppellationsRecap;
    }

    public function getTotauxAgregeByCouleur($totauxByAppellationsRecap, $app_key, $appellation) {
        if (preg_match('/^PINOTNOIR/', $app_key)) {
            return $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEROUGEROSE', $appellation, 'Rouge ou Rosé');
        }
        if(!$appellation->getConfig()->existRendementCouleur()) {
          
          return $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEBLANC', $appellation, 'AOC Alsace Blanc');
        }
        foreach ($appellation->getLieux() as $lieu) {
              foreach ($lieu->getCouleurs() as $couleur_key => $couleur) {
                  if (preg_match('/Rouge$/', $couleur_key)) {
                      $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEROUGEROSE', $couleur, 'Rouge ou Rosé');
                  } else {
                      $totauxByAppellationsRecap = $this->getTotauxWithNode($totauxByAppellationsRecap, 'ALSACEBLANC', $couleur, 'AOC Alsace Blanc');
                  }
              }
        }
        return $totauxByAppellationsRecap;
    }

    public function getTotauxWithNode($totauxByAppellationsRecap, $key, $node, $nom) {
        if (!array_key_exists($key, $totauxByAppellationsRecap)) {
            $totauxByAppellationsRecap[$key] = new stdClass();
            $totauxByAppellationsRecap[$key]->nom = 'TOTAL ' . $nom;
            $totauxByAppellationsRecap[$key]->revendique_sur_place = null;
            $totauxByAppellationsRecap[$key]->usages_industriels_sur_place = null;
        }

        if (!$node) {

            return $totauxByAppellationsRecap;
        }

        $totauxByAppellationsRecap[$key]->revendique_sur_place += ($node->getVolumeRevendiqueCaveParticuliere()) ? $node->getVolumeRevendiqueCaveParticuliere() : 0;
        $totauxByAppellationsRecap[$key]->usages_industriels_sur_place += ($node->getUsagesIndustrielsSurPlace()) ? $node->getUsagesIndustrielsSurPlace() : 0;

        $totauxByAppellationsRecap[$key]->revendique_sur_place += $node->getTotalVolumeAcheteurs("mouts");

        return $totauxByAppellationsRecap;
    }
    
}
