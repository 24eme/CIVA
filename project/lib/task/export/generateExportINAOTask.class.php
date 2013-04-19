<?php

class generateExportINAOTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('campagnes', sfCommandArgument::IS_ARRAY, 'Campagnes'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'generate';
    $this->name             = 'export-inao';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportCreateDouanes|INFO] task does things.
Call it with:

  [php symfony export:create-douanes|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $departements = array("6768" => "Bas-Rhin et Haut-Rhin");

    $annees = $arguments['campagnes'];

    $export = ExportClient::getInstance()->find('EXPORT-INAO-6768');

    $cle = null;

    if (!$export) {
      $export = new Export();
      $export->set('_id', 'EXPORT-INAO-6768');
      $export->nom = "INAO";
      $export->destinataire = "INAO";
      $export->identifiant = "6768";
      $export->generateCle();
    }

    $export->drs->remove('views');
    $export->drs->add('views');

    foreach($annees as $annee) {
      $view = $export->drs->views->add();
      $view->id = 'DR';
      $view->nom = 'campagne_declaration_insee';
      $view->startkey = array($annee, '67000', '0000000000');
      $view->endkey = array($annee, '68999', '9999999999');
    }

    $export->save();
    $this->logSection($export->get('_id'), $export->cle);
  }
}
