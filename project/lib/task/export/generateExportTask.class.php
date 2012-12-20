<?php

class generateExportTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('nom', sfCommandArgument::REQUIRED, 'Nom'),
      new sfCommandArgument('destinataire', sfCommandArgument::REQUIRED, 'Destinataire'),
      new sfCommandArgument('identifiant', sfCommandArgument::REQUIRED, 'Identifiant'),
      new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Campagne'),
      new sfCommandArgument('file_cvis', null, sfCommandOption::PARAMETER_REQUIRED, 'file path contain CVIs to export', null),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      new sfCommandOption('delete', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', false),
    ));

    $this->namespace        = 'generate';
    $this->name             = 'export';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportCreate|INFO] task does things.
Call it with:

  [php symfony export:create|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $ids = array();

    if (!is_file($arguments['file_cvis'])) {
        throw new sfCommandArgumentsException("File does not exist");
    }

    foreach (file($arguments['file_cvis']) as $line) {
      $cvi = trim(str_replace("\n", "", $line));
      $ids[] = sprintf("DR-%s-%s", $cvi, $arguments['campagne']);
    }
    

    $id = 'EXPORT-'.strtoupper($arguments['destinataire'])."-".strtoupper($arguments['identifiant']);

    $export = ExportClient::getInstance()->retrieveDocumentById('EXPORT-'.strtoupper($arguments['destinataire'])."-".strtoupper($arguments['identifiant']));

    if (!$export) {
      $export = new Export();
      $export->set('_id', $id);
      $export->nom = $arguments['nom'];
      $export->destinataire = $arguments['destinataire'];
      $export->identifiant = $arguments['identifiant'];
      $export->generateCle();
    }

    $export->drs->ids = $ids;

    $export->save();
    $this->logSection($export->get('_id'), $export->cle);
  }
}
