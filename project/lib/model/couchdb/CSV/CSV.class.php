<?php

class CSV extends baseCSV
{
  public function storeCSV(CsvFile $csv) {
    $ids = array();
    foreach ($csv->getCsv() as $ligne) {
      $ids[$ligne[CsvFile::CSV_RECOLTANT_CVI]] = 1;
    }
    $this->recoltants = array_keys($ids);
    $this->save();
    $this->storeAttachment($csv->getFileName(), 'text/csv', $this->getCsvFilename());
    $this->save();
  }

  public function getCsvFilename() {
    return $this->campagne.'-'.$this->cvi.'.csv';
  }

  public function getCsvFile() {
    $csv = new CsvFileAcheteur($this->getAttachmentUri($this->getCsvFilename()));
    return $csv;
  }

  public function getCsvRecoltant($cvi) {
    return $this->getCsvFile()->getCsvRecoltant($cvi);
  }

}
