<?php

class importLieuxStockageTask extends importAbstractTask
{

  const CSV_CVI = 0;
  const CSV_RAISON_SOCIALE = 1;
  const CSV_CVI_PREC = 2;
  const CSV_NUMERO_INSTALLATION = 3;
  const CSV_ADRESSE = 4;
  const CSV_CODE_POSTAL_COMMUNE = 5;
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
    foreach(file($arguments['file']) as $line) {
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
    $tiers =  acCouchdbManager::getClient('Recoltant')->retrieveByCvi($cvi);

    if(!$tiers) {
      $this->logLignes("ERROR", sprintf("Récoltant '%s' introuvable", $cvi), $lines);

      return;
    }

    foreach($lines as $i => $line) {
      try{
        $this->importLieuStockage($tiers, $line);
      } catch (Exception $e) {
        $this->logLigne("ERROR", $e->getMessage(), $line, $i);
        return;
      }
    }

    try{
      $tiers->save();
    } catch (Exception $e) {
      $this->logLignes("ERROR", $e->getMessage(), $lines, $i);
    }
  }

  public function importLieuStockage($tiers, $line) {
    if(!preg_match(sprintf('/^%s/', $line[self::CSV_CVI]), $line[self::CSV_NUMERO_INSTALLATION])) {

      throw new sfException(sprintf("Le CVI '%s' n'est pas compris dans le numéro d'installation '%s'", $line[self::CSV_CVI], $line[self::CSV_NUMERO_INSTALLATION]));
    }

    $lieu_stockage = $tiers->add('lieux_stockage')->add($line[self::CSV_NUMERO_INSTALLATION]);
  
    $lieu_stockage->numero = $line[self::CSV_NUMERO_INSTALLATION];
    $lieu_stockage->nom = $line[self::CSV_RAISON_SOCIALE];
    $lieu_stockage->adresse = $line[self::CSV_ADRESSE];
    $lieu_stockage->commune = substr($line[self::CSV_CODE_POSTAL_COMMUNE], (strlen($line[self::CSV_CODE_POSTAL_COMMUNE]) -6) * -1);
    $lieu_stockage->code_postal  = substr($line[self::CSV_CODE_POSTAL_COMMUNE], 0, 5);
  }

}
