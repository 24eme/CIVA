<?php

class CsvValidatedFile extends sfValidatedFile 
{
  protected $csv = null;
  protected $md5 = null;

  public function __construct(sfValidatedFile $file) {
    parent::__construct($file->getOriginalName(), $file->getType(), $file->getTempName(), $file->getSize(), $file->getPath());
    $this->csv = null;
  }
  public function setMd5($m) {
    $this->md5 = $m;
  }
  public function getMd5() {
    return $this->md5;
  }
  public function setCsv($csv) {
    $this->csv = $csv;
  }
  public function getCsv() {
    return $this->csv->getCsv();
  }
}

