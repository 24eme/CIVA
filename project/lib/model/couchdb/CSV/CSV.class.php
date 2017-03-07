<?php

class CSV extends baseCSV
{
  public function storeCSV(CsvFileAcheteur $csv) {
    $ids = array();
    foreach ($csv->getCsv() as $ligne) {
      $ids[$ligne[CsvFileAcheteur::CSV_RECOLTANT_CVI]] = 1;
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

  public function clearErreurs() {
      $this->remove('erreurs');
      $this->add('erreurs');
      $this->statut = null;
  }

  public function getFileContent() {
      return file_get_contents($this->getAttachmentUri($this->getFileName()));
  }

  public function getFileName($partiel = true) {
      return 'import_partiel_edi_' . $this->identifiant . '_' . $this->periode . '.csv';
  }

  public function hasErreurs() {
      return count($this->erreurs);
  }

}
