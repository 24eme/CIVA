<?php

class generateExportMairiesTask extends sfBaseTask
{
  protected function configure()
  {
  
    $this->addArguments(array(
       new sfCommandArgument('campagnes_dr', sfCommandArgument::REQUIRED, 'Campagnes pour la DR séparé par des "," (exemple 2010,2011,2012)'),
       new sfCommandArgument('campagnes_ds', sfCommandArgument::REQUIRED, 'Campagnes pour la DS séparé par des "," (exemple 2010,2011,2012)'),
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

    $annees_dr = explode(",", $arguments['campagnes_dr']);
    $annees_ds = explode(",", $arguments['campagnes_ds']);

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

        $export->remove('drs');
        $export->add('drs');

        foreach($annees_dr as $annee) {
          $view = $export->drs->views->add();
          $view->id = 'DR';
          $view->nom = 'campagne_declaration_insee';
          $view->startkey = array($annee, $code_postal);
          $view->endkey = array($annee, $code_postal, '[]');
        }

        $export->remove('dss');
        $export->add('dss');

        foreach($annees_ds as $annee) {
          $view = $export->dss->views->add();
          $view->id = 'DS';
          $view->nom = 'campagne_declaration_insee';
          $view->startkey = array($annee,(string) $code_postal);
          $view->endkey = array($annee,(string) $code_postal, '[]');
        }
        
        $export->save();
        $this->logSection($export->get('_id'), $export->cle);
    }

  }
}
