<?php

class CSVClient extends sfCouchdbClient {
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