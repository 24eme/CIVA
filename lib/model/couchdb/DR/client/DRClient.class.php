<?php

class DRClient extends sfCouchdbClient {
  public function createFromCSVRecoltant($tiers, &$import) {
    $csvs = sfCouchdbManager::getClient('CSV')->getCSVsFromRecoltant($tiers->cvi);
    if (!$csvs || !count($csvs))
      throw new sfException('no csv found for '.$tiers->cvi) ;
    $campagne = $csvs[0]->campagne;
    $doc = new DR();
    $doc->set('_id', 'DR-' . $tiers->cvi . '-' . $campagne);
    $doc->cvi = $tiers->cvi;
    $doc->campagne = $campagne;
    $doc->declaration_insee = $tiers->declaration_insee;
    $doc->declaration_commune = $tiers->declaration_commune;
    foreach ($csvs as $csv) {
      $import[] = sfCouchdbManager::getClient('Acheteur')->retrieveByCvi($csv->cvi);
      foreach ($csv->getCsvRecoltant($tiers->cvi) as $line) {
	if ($line[CsvFile::CSV_APPELLATION] == 'JEUNES VIGNES') {
	  $doc->jeunes_vignes = $line[CsvFile::CSV_SUPERFICIE]*1;
	  continue;
	}
	$prod = ConfigurationClient::getConfiguration()->identifyProduct($line[CsvFile::CSV_APPELLATION], 
									 $line[CsvFile::CSV_LIEU], 
									 $line[CsvFile::CSV_CEPAGE]);
	if (!isset($prod['hash']))
	  throw new sfException($prod['error']);
	
	$cepage = $doc->getOrAdd($prod['hash']);

	$denomlieu = '';
	if ($cepage->getLieu()->getKey() == 'lieu')
	  $denomlieu = $line[CsvFile::CSV_LIEU];
	echo "denomlieu : $denomlieu<br>";
	$detail = $cepage->retrieveDetailFromUniqueKeyOrCreateIt($line[CsvFile::CSV_DENOMINATION], $line[CsvFile::CSV_VTSGN], $denomlieu);
	$detail->superficie += $line[CsvFile::CSV_SUPERFICIE]*1;
	$detail->volume += $line[CsvFile::CSV_VOLUME]*1;
	if ($line[CsvFile::CSV_VOLUME]*1 == 0) {
	  $detail->denomination = 'repli';
	  $detail->add('motif_non_recolte', 'AE');
	}else{
	  $acheteur = $detail->add('cooperatives')->add();
	  $acheteur->cvi = $line[CsvFile::CSV_ACHETEUR_CVI];
	  $acheteur->quantite_vendue = $line[CsvFile::CSV_VOLUME]*1;
	}
	$detail->volume_dplc += $line[CsvFile::CSV_VOLUME_DPLC]*1;
      }
    }
    $doc->update();
    return $doc;
  }
    public function retrieveByCampagneAndCvi($cvi, $campagne, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::retrieveDocumentById('DR-'.$cvi.'-'.$campagne, $hydrate);
    }

    public function getAllByCampagne($campagne, $hydrate = sfCouchdbClient::HYDRATE_ON_DEMAND) {
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

    public function getAllByCvi($cvi, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
       return $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-2020')->execute($hydrate);
    }

    /**
     *
     * @param string $cvi
     * @param string $campagne
     * @return array 
     */
    public function getArchivesSince($cvi, $campagne) {
        $docs = $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-'.$campagne)->execute(sfCouchdbClient::HYDRATE_ON_DEMAND);
        $campagnes = array();
        foreach($docs->getIds() as $doc_id) {
            preg_match('/DR-(?P<cvi>\d+)-(?P<campagne>\d+)/', $doc_id, $matches);
            $campagnes[$doc_id] = $matches['campagne'];
        }
        return $campagnes;
    }

    public function getAll($hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('DR-0000000000-0000')->endkey('DR-9999999999-9999')->execute($hydrate);
    }
    
    public function findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey(array($campagne, $cvi_acheteur))->endkey(array($campagne, (string)($cvi_acheteur + 1)))->executeView("DR", "campagne_acheteur", $hydrate);
    }
    
    public function findAllByCampagneAcheteurs($campagne, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey(array((string)$campagne))->endkey(array((string)($campagne+1)))->executeView("DR", "campagne_acheteur", $hydrate);
    }
}
