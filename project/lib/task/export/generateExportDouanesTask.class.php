<?php

class generateExportDouanesTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
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
    $this->name             = 'export-douanes';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportCreateDouanes|INFO] task does things.
Call it with:

  [php symfony export:create-douanes|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {

    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $departements = array("6768" => "Bas-Rhin et Haut-Rhin");

    $annees_dr = explode(",", $arguments['campagnes_dr']);
    $annees_ds = explode(",", $arguments['campagnes_ds']);

    $export = ExportClient::getInstance()->find('EXPORT-DOUANES-6768');

    $cle = null;

    if (!$export) {
      $export = new Export();
      $export->set('_id', 'EXPORT-DOUANES-6768');
      $export->nom = $departements["6768"];
      $export->destinataire = 'Douanes';
      $export->identifiant = "6768";
      $export->generateCle();
    }

    $export->remove('drs');
    $export->add('drs');

    foreach($annees_dr as $annee) {
      $view = $export->dss->views->add();
      $view->id = 'DR';
      $view->nom = 'campagne_declaration_insee';
      $view->startkey = array($annee);
      $view->endkey = array($annee, '[]');
    }

    $export->remove('dss');
    $export->add('dss');

    foreach($annees_ds as $annee) {
      $view = $export->dss->views->add();
      $view->id = 'DS';
      $view->nom = 'campagne_declaration_insee';
      $view->startkey = array($annee);
      $view->endkey = array($annee, '[]');
    }

    $export->save();
    $this->logSection($export->get('_id'), $export->cle);
  }
}
