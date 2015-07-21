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
      
      if($cvi && $cvi != $data[self::CSV_CVI]) {
        $this->importLieuxStockage($cvi, $lines);
        $lines = array();
      }
      
      $cvi = $data[self::CSV_CVI];
      $lines[$i] = $data;
      $i++;
    }

    if($cvi && count($lines) > 0) {
      $this->importLieuxStockage($cvi, $lines);
    }

  }

  public function importLieuxStockage($cvi, $lines) {
    $tiers = acCouchdbManager::getClient('Recoltant')->retrieveByCvi($cvi);

    if(!$tiers) {
      $tiers = acCouchdbManager::getClient('Acheteur')->retrieveByCvi($cvi);
      if($tiers && !$tiers->isDeclarantStockPropriete()) {
        $tiers = null;
      }

      if($tiers) {
        $this->logLignes("INFO", sprintf("Cave coop %s", $cvi), $lines);
      }
    }

    if(!$tiers) {
      $this->logLignes("ERROR", sprintf("Récoltant ou cave coop '%s' introuvable", $cvi), $lines);

      return;
    }

    $tiers->remove('lieux_stockage');
    $tiers->add('lieux_stockage');
    foreach($lines as $i => $line) {
      try{
        $this->importLieuStockage($tiers, $line);
      } catch (Exception $e) {
        $this->logLigne("ERROR", $e->getMessage(), $line, $i);
        return;
      }
    }

    try{
      if($tiers->isModified()) {
        $this->logLigne("MODIFIED", "", $line, $i);
      }
      $tiers->save();
    } catch (Exception $e) {
      $this->logLignes("ERROR", $e->getMessage(), $lines, $i);
    }
  }

  public function importLieuStockage($tiers, $line) {
    if(!preg_match(sprintf('/^%s/', $line[self::CSV_CVI]), $line[self::CSV_NUMERO_INSTALLATION])) {

      throw new sfException(sprintf("Le CVI '%s' n'est pas compris dans le numéro d'installation '%s'", $line[self::CSV_CVI], $line[self::CSV_NUMERO_INSTALLATION]));
    }

    $lieu_stockage = $tiers->add('lieux_stockage')->add(trim($line[self::CSV_NUMERO_INSTALLATION]));
  
    $lieu_stockage->numero = trim($line[self::CSV_NUMERO_INSTALLATION]);
    $lieu_stockage->nom = trim($line[self::CSV_RAISON_SOCIALE]);



    if(trim($line[self::CSV_CODE_POSTAL])) {
      $lieu_stockage->code_postal = trim($line[self::CSV_CODE_POSTAL]);
    } else {
      $lieu_stockage->code_postal = $tiers->siege->code_postal;
    }
    $lieu_stockage->commune  = trim($line[self::CSV_COMMUNE]);

    $lieu_stockage->adresse = trim(preg_replace("/(".$lieu_stockage->commune."|".$lieu_stockage->code_postal.")$/", "", trim($line[self::CSV_ADRESSE])));
  }

}
