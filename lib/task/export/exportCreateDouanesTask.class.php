<?php

class exportCreateDouanesTask extends sfBaseTask
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
    $this->name             = 'create-douanes';
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

    $departements = array("67" => "Bas-Rhin", 
                          "68" => "Haut-Rhin");

    $annees = array("2011");

    foreach($departements as $departement => $nom) {
        $export = ExportClient::getInstance()->retrieveDocumentById('EXPORT-DOUANES-'. $departement , sfCouchdbClient::HYDRATE_JSON);

        $cle = null;

        if ($export && $options['delete']) {
          $cle = $export->cle;
          sfCouchdbManager::getClient()->deleteDoc($export);
          $export = null;
        }

        if (!$export) {
          $export = new Export();
          $export->set('_id', 'EXPORT-DOUANES-' . $departement);
          $export->nom = $nom . ' (' . $departement . ')';
          $export->destinataire = 'Douanes';
          $export->identifiant = $departement;
          
          foreach($annees as $annee) {
            $view = $export->drs->views->add();
            $view->id = 'DR';
            $view->nom = 'campagne_declaration_insee';
            $view->startkey = array($annee, $departement.'000', '0000000000');
            $view->endkey = array($annee, $departement.'999', '9999999999');
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
