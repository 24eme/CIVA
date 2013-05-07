<?php

class generateExportMairiesTask extends sfBaseTask
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
    $this->name             = 'export-mairies';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportCreateMairies|INFO] task does things.
Call it with:

  [php symfony export:create-mairies|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $csv = array();
    $communes = array();
    foreach (file(sfConfig::get('sf_data_dir') . '/import/communes_declaration.csv') as $c) {
        $csv = explode(';', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
        $communes[$csv[0]] = $csv[1];
    }

    $annees = $arguments['campagnes'];

    foreach($communes as $code_postal => $nom) {
        $export = ExportClient::getInstance()->find('EXPORT-MAIRIES-'. $code_postal);

        $cle = null;

        if (!$export) {
          $export = new Export();
          $export->set('_id', 'EXPORT-MAIRIES-' . $code_postal);
          $export->nom = $nom . ' (' . $code_postal . ')';
          $export->destinataire = 'Mairies';
          $export->identifiant = $code_postal;
          $export->generateCle();
        }

        $export->drs->remove('views');
        $export->drs->add('views');

        foreach($annees as $annee) {
          $view = $export->drs->views->add();
          $view->id = 'DR';
          $view->nom = 'campagne_declaration_insee';
          $view->startkey = array($annee, $code_postal, '0000000000');
          $view->endkey = array($annee, $code_postal, '9999999999');
        }
        
        $export->save();
        $this->logSection($export->get('_id'), $export->cle);
    }

  }
}
