<?php

class importLieuxStockageTask extends importAbstractTask
{

  const CSV_CVI = 0;
  const CSV_RAISON_SOCIALE = 1;
  const CSV_NUMERO_INSTALLATION = 2;
  const CSV_ADRESSE = 3;
  const CSV_CODE_POSTAL = 4;
  const CSV_COMMUNE = 5;
  const CSV_TYPE_INSTALLATION = 6;

  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('file', sfCommandArgument::REQUIRED, "Fichier csv pour l'import"),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'import';
    $this->name             = 'LieuxStockages';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [importVrac|INFO] task does things.
Call it with:

  [php symfony importLieuxStockage|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    set_time_limit(0);
    $i = 1;
    $cvi = null;
    $lines = array();
    $file = file($arguments['file']);
    //$file[] = "7523700100;GARAGE ACTUALYS;7523700100001;1 RUE GARNIER;75000 PARIS;Installation Mixte\n";
    //$file[] = "7523700100;CAVE ACTUALYS;7523700100002;1 RUE DES VIGNES;75000 PARIS;Installation Mixte\n";

    foreach($file as $line) {
      $data = str_getcsv($line, ';');

      if($cvi && $cvi != trim($data[self::CSV_CVI])) {
        $this->importLieuxStockage($cvi, $lines);
        $lines = array();
      }

      $cvi = trim($data[self::CSV_CVI]);

      $lines[$i] = $data;
      $i++;
    }

    if($cvi && count($lines) > 0) {
      $this->importLieuxStockage($cvi, $lines);
    }

  }

  public function importLieuxStockage($cvi, $lines) {
    $etablissement = EtablissementClient::getInstance()->findByCvi($cvi);

    if(!$etablissement) {
      $this->logLignes("ERROR", sprintf("Pas trouvé", $cvi), $lines);

      return;
    }

    if(!$etablissement->hasDroit(Roles::TELEDECLARATION_DS_PROPRIETE)) {
      $this->logLignes("ERROR", sprintf("Ne fait pas de DS propriété", $cvi), $lines);

      return;
    }

    if(!$etablissement->isActif()) {
        $this->logLignes("ERROR", sprintf("L'établissement n'est pas actif", $cvi), $lines);

        return;
    }

    if($etablissement->getFamille() == EtablissementFamilles::FAMILLE_COOPERATIVE) {
      $this->logLignes("INFO", sprintf("Cave coop %s", $cvi), $lines);
    }

    $etablissement->removeLieuxStockage($cvi);

    foreach($lines as $i => $line) {
      try{
        $this->importLieuStockage($etablissement, $line);
      } catch (Exception $e) {
        $this->logLigne("ERROR", $e->getMessage(), $line, $i);
        return;
      }
    }

    if(!count($etablissement->getLieuxStockage(false, $cvi))) {
        $this->logLignes("ERROR", "Établissement sans lieu de stockage", $lines);
        return;
    }

    try{
      if($etablissement->isModified()) {
        $this->logLignes("SUCCESS", "", $lines, $i);
        $etablissement->save();
      }
    } catch (Exception $e) {
      $this->logLignes("ERROR", $e->getMessage(), $lines, $i);
    }
  }

  public function importLieuStockage($etablissement, $line) {
    if(!preg_match(sprintf('/^%s/', $line[self::CSV_CVI]), $line[self::CSV_NUMERO_INSTALLATION])) {

      throw new sfException(sprintf("Le CVI '%s' n'est pas compris dans le numéro d'installation '%s'", $line[self::CSV_CVI], $line[self::CSV_NUMERO_INSTALLATION]));
    }

    if(preg_match("/supprimée/", $line[self::CSV_COMMUNE])) {
        $this->logLignes("WARNING", sprintf("Le lieu de stockage a été supprimée"), array($line));
        return;
    }

    if(trim($line[self::CSV_CODE_POSTAL])) {
      $code_postal = trim($line[self::CSV_CODE_POSTAL]);
    } else {
      $code_postal = $etablissement->siege->code_postal;
    }

    $commune = trim($line[self::CSV_COMMUNE]);
    $adresse = strtoupper(trim($line[self::CSV_ADRESSE]));
    $adresse = strtoupper(trim(preg_replace("/".$commune."$/", "", $adresse)));
    $adresse = strtoupper(trim(preg_replace("/".$code_postal."$/", "", $adresse)));
    $adresse = strtoupper(trim(preg_replace("/".$commune."$/", "", $adresse)));

    $lieu_stockage = $etablissement->add('lieux_stockage')->add(trim($line[self::CSV_NUMERO_INSTALLATION]));

    $lieu_stockage->numero = trim($line[self::CSV_NUMERO_INSTALLATION]);
    $line[self::CSV_RAISON_SOCIALE] = str_replace("&amp;", "&", $line[self::CSV_RAISON_SOCIALE]);
    $line[self::CSV_RAISON_SOCIALE] = str_replace("amp;", "&", $line[self::CSV_RAISON_SOCIALE]);
    $line[self::CSV_RAISON_SOCIALE] = trim($line[self::CSV_RAISON_SOCIALE]);
    $line[self::CSV_RAISON_SOCIALE] = preg_replace("/[ ]+/", " ", $line[self::CSV_RAISON_SOCIALE]);
    $lieu_stockage->nom = $line[self::CSV_RAISON_SOCIALE];

    $lieu_stockage->code_postal  = $code_postal;
    $lieu_stockage->commune  = $commune;

    $lieu_stockage->adresse = $adresse;
  }

}
