<?php

class CsvValidatedFile extends sfValidatedFile 
{
  protected $csvfile = null;
  protected $csvdata = null;
  protected $separator = null;

  public function __construct(sfValidatedFile $file) {
    parent::__construct($file->getOriginalName(), $file->getType(), $file->getTempName(), $file->getSize(), $file->getPath());
    $this->csvfile = null;
    $this->csvdata = null;
    $this->separator = null;
  }
  public function setSeparator($s) {
    $this->separator = $s;
  }
  public function setCsvFile($csv) {
    $this->csvfile = $csv;
  }
  public function getCsv() {
    if ($this->csvdata)
      return $this->csvdata;
    $handle = fopen($this->csvfile, 'r');
    $this->csvdata = array();
    while (($data = fgetcsv($handle, 0, $this->separator)) !== FALSE) {
      $this->csvdata[] = $data;
    }
    fclose($handle);
    return $this->csvdata;
  }
}

