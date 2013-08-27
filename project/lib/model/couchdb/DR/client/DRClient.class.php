<?php

class DRClient extends acCouchdbClient {
  
  public static function getInstance() {

    return acCouchdbManager::getClient('DR');
  }

  public function createFromCSVRecoltant($campagne, $tiers, &$import) {
    $csvs = acCouchdbManager::getClient('CSV')->getCSVsFromRecoltant($campagne, $tiers->cvi);
    if (!$csvs || !count($csvs))
      throw new sfException('no csv found for '.$tiers->cvi) ;
    $campagne = $csvs[0]->campagne;
    $doc = new DR();
    $doc->set('_id', 'DR-' . $tiers->cvi . '-' . $campagne);
    $doc->cvi = $tiers->cvi;
    $doc->campagne = $campagne;
    $doc->declaration_insee = $tiers->declaration_insee;
    $doc->declaration_commune = $tiers->declaration_commune;
    $doc->identifiant = $tiers->cvi;
    $doc->storeDeclarant();

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
          $doc->jeunes_vignes = $this->recodeNumber($line[CsvFile::CSV_SUPERFICIE]);
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
        if (isset($line[CsvFile::CSV_VOLUME_DPLC]))
          $detail->volume_dplc += $this->recodeNumber($line[CsvFile::CSV_VOLUME_DPLC]);
        }
    }
    $doc->utilisateurs->edition->add('csv', date('d/m/Y'));
    $doc->update();
    return $doc;
  }

  protected function recodeNumber($value) {

    return str_replace(",", ".", $value)*1;
  }

    public function retrieveByCampagneAndCvi($cvi, $campagne, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
      return parent::find('DR-'.$cvi.'-'.$campagne, $hydrate);
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
    public function getArchivesSince($cvi, $campagne) {
        $docs = $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-'.$campagne)->execute(acCouchdbClient::HYDRATE_ON_DEMAND);
        $campagnes = array();
        foreach($docs->getIds() as $doc_id) {
            preg_match('/DR-(?P<cvi>\d+)-(?P<campagne>\d+)/', $doc_id, $matches);
            $campagnes[$doc_id] = $matches['campagne'];
        }
        return $campagnes;
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
    
}
