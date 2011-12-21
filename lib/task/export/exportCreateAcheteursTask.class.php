<?php

class exportCreateAcheteursTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      new sfCommandOption('delete', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', false),
    ));

    $this->namespace        = 'export';
    $this->name             = 'create-acheteurs';
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

    $annees = array("2011");

    $ids = CSVClient::getInstance()->findAll(sfCouchdbClient::HYDRATE_JSON)->getIds();

    foreach($ids as $id) {

      $csv = sfCouchdbManager::getClient()->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
      $acheteur = AcheteurClient::getInstance()->retrieveByCvi($csv->cvi, sfCouchdbClient::HYDRATE_JSON);
      $compte = sfCouchdbManager::getClient()->retrieveDocumentById($acheteur->compte[0], sfCouchdbClient::HYDRATE_JSON);
      $export = ExportClient::getInstance()->retrieveDocumentById('EXPORT-ACHETEURS-'. $acheteur->cvi , sfCouchdbClient::HYDRATE_JSON);

      if ($export && $options['delete']) {
         sfCouchdbManager::getClient()->deleteDoc($export);
         $export = null;
      }

      if (!$export) {
        $export = new Export();
        $export->set('_id', 'EXPORT-ACHETEURS-' . $acheteur->cvi);
        $export->compte = $compte->_id;
        $export->nom = $acheteur->nom;
        $export->destinataire = 'Acheteurs';
        $export->identifiant = $acheteur->cvi;

        foreach($csv->recoltants as $cvi) {
          $export->drs->ids->add(null, 'DR-' . $cvi . '-' .$csv->campagne);
        }
        
        $export->generateCle();
        $export->save();
        $this->logSection($export->get('_id'), 'created');
      }
    }

  }
}
