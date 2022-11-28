<?php

class CsvFileAcheteur 
{
  const CSV_ACHETEUR_CVI = 0;
  const CSV_ACHETEUR_LIBELLE = 1;
  const CSV_RECOLTANT_CVI = 2;
  const CSV_RECOLTANT_LIBELLE = 3;
  const CSV_APPELLATION = 4;
  const CSV_LIEU = 5;
  //  const CSV_COULEUR = 6;
  const CSV_CEPAGE = 6;
  const CSV_VTSGN = 7;
  const CSV_DENOMINATION = 8;
  const CSV_SUPERFICIE = 9;
  const CSV_VOLUME = 10;
  const CSV_VOLUME_DPLC = 11;
  const CSV_VOLUME_VCI = 15;

  const CSV_SV_QUANTITE_VF = 10;
  const CSV_SV_VOLUME_VF = 11;
  const CSV_SV_VOLUME_DPLC = 12;
  const CSV_SV_VOLUME_VCI = 13;
  const CSV_SV_VOLUME_PRODUIT = 14;

  private $file = null;
  private $separator = null;
  private $csvdata = null;
  private $ignore = null;

  public function getFileName() {
    return $this->file;
  }

  public function __construct($file, $ignore_first_if_comment = 1) {
    $this->ignore = $ignore_first_if_comment;
    if (!file_exists($file) && !preg_match('/^http/', $file))
      throw new Exception("Cannont access $file");

    $this->file = $file;
    $handle = fopen($this->file, 'r');
    if (!$handle)
      throw new Exception('invalid_file');
    $buffer = fread($handle, 500);
    fclose($handle);
    $buffer = preg_replace('/$[^\n]*\n/', '', $buffer);
    if (!$buffer) {
      throw new Exception('invalid_file');
    }
    if (!preg_match('/("?)[0-9]{10}("?)([,;\t])/', $buffer, $match)) {
      throw new Exception('invalid_csv_file');
    }
    $this->separator = $match[3];
  }

  public function getCsvRecoltant($cvi) {
    $lignes = array();
    foreach ($this->getCsv() as $line) {
      if ($line[self::CSV_RECOLTANT_CVI] == $cvi)
	$lignes[] = $line;
    }
    return $lignes;
  }

  private static function clean($array) {
    for($i = 0 ; $i < count($array) ; $i++) {
      $array[$i] = preg_replace('/^ +/', '', preg_replace('/ +$/', '', $array[$i]));
    }
    return $array;
  }

  public function getCsv() {
    if ($this->csvdata)
      return $this->csvdata;

    $handler = fopen($this->file, 'r');
    if (!$handler)
      throw new Exception('Cannot open csv file anymore');
    $this->csvdata = array();
    while (($data = fgetcsv($handler, 0, $this->separator)) !== FALSE) {
      $this->csvdata[] = self::clean($data);
    }
    fclose($handler);
    if ($this->ignore && !preg_match('/^\d{10}$/', $this->csvdata[0][0]))
      array_shift($this->csvdata);
    return $this->csvdata;
  }

  public static function identifyProductCSV($line) {
      $appellation = $line[CsvFileAcheteur::CSV_APPELLATION];
      $appellation = preg_replace("/^0$/", "", $appellation);
      $appellation = preg_replace("/AOC ALSACE PINOT NOIR ROUGE/i", "AOC Alsace PN rouge", $appellation);

      $lieu = $line[CsvFileAcheteur::CSV_LIEU];
      $lieu = preg_replace("/^0$/", "", $lieu);

      $cepage = $line[CsvFileAcheteur::CSV_CEPAGE];
      $cepage = preg_replace("/^0$/", "", $cepage);
      $cepage = preg_replace("/Gewurzt\./i", "Gewurztraminer", $cepage);
      $cepage = preg_replace("/Muscat d'Alsace/i", "Muscat", $cepage);
      $cepage = preg_replace("/^Klevener/i", "Klevener de Heiligenstein ", $cepage);
      $cepage = preg_replace("/Rebêches Blanc/i", "Rebêches", $cepage);
      $cepage = preg_replace("/Rebêches Rouge/i", "Rebêches", $cepage);
      $cepage = preg_replace("/Rebêches Rosé/i", "Rebêches", $cepage);

      if(preg_match("/(AOC ALSACE PINOT NOIR|AOC ALSACE PN ROUGE)/i", $appellation)) {
          $cepage = null;
      }

      $vtsgn = $line[CsvFileAcheteur::CSV_VTSGN];
      $vtsgn = preg_replace("/^0$/", "", $vtsgn);

      $produit = ConfigurationClient::getConfiguration()->identifyProductByLibelle(trim(sprintf("%s %s %s %s", $appellation, $lieu, $cepage, $vtsgn)));

      if(!$produit) {
          $produit = ConfigurationClient::getConfiguration()->identifyProductByLibelle(trim(sprintf("%s %s %s", $appellation, $cepage, $vtsgn)));
      }

      return $produit;
  }

  public static function recodeNumber($value) {
      if(!$value) {

          return 0;
      }
      return round(str_replace(",", ".", $value)*1, 2);
  }
}
