<?php

class CSVClient extends sfCouchdbClient {
  private function getCSVsFromRecoltantArray($cvi) {
    $csv = $this->startkey(array($cvi))->endkey(array(($cvi+1).''))->executeView('import', 'csv');
    $ids = array();
    foreach ($csv as $k => $c) 
      $ids[] = $k;
    return $ids;
  }

  public function countCSVsFromRecoltant($cvi) {
    return count($this->getCSVsFromRecoltantArray($cvi));
  }

  public function getCSVsFromRecoltant($cvi) {
    $docs = array();
    foreach($this->getCSVsFromRecoltantArray($cvi) as $id) {
      $docs[] = parent::retrieveDocumentById($id);
    }
    return $docs;
  }
  public function retrieveByCviAndCampagneOrCreateIt($cvi, $campagne = null, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
    if (!$campagne) 
      $campagne = CurrentClient::getCurrent()->campagne;
    $csv = $this->retrieveByCviAndCampagne($cvi, $campagne, $hydrate);
    if (!$csv) {
      $csv = new CSV();
      $csv->set('_id', 'CSV-'.$cvi.'-'.$campagne);
      $csv->campagne = $campagne;
      $csv->cvi = $cvi;
      $csv->type = 'CSV';
    }
    return $csv;
  }
  public function retrieveByCviAndCampagne($cvi, $campagne = null, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
    return parent::retrieveDocumentById('CSV-'.$cvi.'-'.$campagne, $hydrate);
  }
  
}