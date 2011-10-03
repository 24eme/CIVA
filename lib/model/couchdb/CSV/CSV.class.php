<?php

class CSV extends baseCSV 
{
  public function storeCSV(CsvFile $csv) {
    $ids = array();
    foreach ($csv->getCsv() as $ligne) {
      $ids[$ligne[2]] = 1;
    }
    $this->recoltants = array_keys($ids);
    $this->save();
    $this->storeAttachment($csv->getFileName(), 'text/csv', $this->campagne.'-'.$this->cvi.'.csv');
    $this->save();
  }

}