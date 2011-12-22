<?php

class exportCreateMairiesTask extends sfBaseTask
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
    $this->name             = 'create-mairies';
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
    $insee = array();
    foreach (file(sfConfig::get('sf_data_dir') . '/import/Commune') as $c) {
        $csv = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
        $insee[$csv[0]] = $csv[1];
    }

    $annees = array("2011");

    foreach($insee as $code_postal => $nom) {
        $export = ExportClient::getInstance()->retrieveDocumentById('EXPORT-MAIRIES-'. $code_postal , sfCouchdbClient::HYDRATE_JSON);

        $cle = null;

        if ($export && $options['delete']) {
          $cle = $export->cle;
          sfCouchdbManager::getClient()->deleteDoc($export);
          $export = null;
        }

        if (!$export) {
          $export = new Export();
          $export->set('_id', 'EXPORT-MAIRIES-' . $code_postal);
          $export->nom = $nom . ' (' . $code_postal . ')';
          $export->destinataire = 'Mairies';
          $export->identifiant = $code_postal;

          foreach($annees as $annee) {
            $view = $export->drs->views->add();
            $view->id = 'DR';
            $view->nom = 'campagne_declaration_insee';
            $view->startkey = array($annee, $code_postal, '0000000000');
            $view->endkey = array($annee, $code_postal, '9999999999');
          }
          
          $export->cle = $cle;
          if (!$export->cle) {
            $export->generateCle();
          }

          $export->save();
          $this->logSection($export->get('_id'), 'created');
        }
    }

  }
}
