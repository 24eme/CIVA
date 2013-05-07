<?php

class generateExportAcheteursTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Campagnes'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'generate';
    $this->name             = 'export-acheteurs';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportCreateAcheteurs|INFO] task does things.
Call it with:

  [php symfony export:create-acheteurs|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $ids = CSVClient::getInstance()->getCSVsAcheteurs($arguments['campagne']);

    foreach($ids as $id) {

      $csv = acCouchdbManager::getClient()->find($id, acCouchdbClient::HYDRATE_JSON);
      $acheteur = AcheteurClient::getInstance()->retrieveByCvi($csv->cvi, acCouchdbClient::HYDRATE_JSON);
      $compte = acCouchdbManager::getClient()->find($acheteur->compte[0], acCouchdbClient::HYDRATE_JSON);
      $export = ExportClient::getInstance()->find('EXPORT-ACHETEURS-'. $acheteur->cvi);

      if (!$export) {
        $export = new Export();
        $export->set('_id', 'EXPORT-ACHETEURS-' . $acheteur->cvi);
        $export->compte = $compte->_id;
        $export->nom = $acheteur->nom;
        $export->destinataire = 'Acheteurs';
        $export->identifiant = $acheteur->cvi;
        $export->generateCle();
      }

      $export->drs->remove('ids');
      $export->drs->add('ids');

      foreach($csv->recoltants as $cvi) {
        $export->drs->ids->add(null, 'DR-' . $cvi . '-' .$csv->campagne);
      }

      $export->save();
      $this->logSection($export->get('_id'), $export->cle);

    }
  }
}
