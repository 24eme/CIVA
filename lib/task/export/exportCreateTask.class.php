<?php

class exportCreateTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('nom', sfCommandArgument::REQUIRED, 'Nom'),
       new sfCommandArgument('destinataire', sfCommandArgument::REQUIRED, 'Destinataire'),
       new sfCommandArgument('identifiant', sfCommandArgument::REQUIRED, 'Identifiant'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      new sfCommandOption('delete', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', false),
      new sfCommandOption('csvfile_ids', null, sfCommandOption::PARAMETER_REQUIRED, 'csv file path contain DRs ids to export', null),
    ));

    $this->namespace        = 'export';
    $this->name             = 'create';
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

    if ($options['csvfile_ids']) {
      if (!is_file($options['csvfile_ids'])) {
        throw new sfCommandArgumentsException("File does not exist");
      }
      foreach (file($options['csvfile_ids']) as $c) {
        $csv = explode(';', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
        $ids[] = $csv[0];
      }
    }

    $id = 'EXPORT-'.strtoupper($arguments['destinataire'])."-".strtoupper($arguments['identifiant']);

    $export = ExportClient::getInstance()->retrieveDocumentById('EXPORT-'.strtoupper($arguments['destinataire'])."-".strtoupper($arguments['identifiant']), sfCouchdbClient::HYDRATE_JSON);

    $cle = null;

    if ($export && $options['delete']) {
      $cle = $export->cle;
      sfCouchdbManager::getClient()->deleteDoc($export);
      $export = null;
    }

    if (!$export) {
      $export = new Export();
      $export->set('_id', $id);
      $export->nom = $arguments['nom'];
      $export->destinataire = $arguments['destinataire'];
      $export->identifiant = $arguments['identifiant'];
      $export->drs->ids = $ids;

      $export->cle = $cle;
      if (!$export->cle) {
        $export->generateCle();
      }

      $export->save();
      $this->logSection($export->get('_id'), 'created');
    }

  }
}
