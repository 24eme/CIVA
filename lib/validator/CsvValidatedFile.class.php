<?php

class CsvValidatedFile extends sfValidatedFile 
{
  protected $csv = null;

  public function __construct(sfValidatedFile $file) {
    parent::__construct($file->getOriginalName(), $file->getType(), $file->getTempName(), $file->getSize(), $file->getPath());
    $this->csv = null;
  }
  public function setCsv($csv) {
    $this->csv = $csv;
  }

  public function getCsv() {
    return $this->csv;
  }
}

