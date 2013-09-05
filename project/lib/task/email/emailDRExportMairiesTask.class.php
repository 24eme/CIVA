<?php

class emailDRExportMairiesTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      new sfCommandOption('send', null, sfCommandOption::PARAMETER_REQUIRED, 'Send', false),
    ));

    $this->namespace        = 'email';
    $this->name             = 'dr-export-mairies';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [emailDRExportMairies|INFO] task does things.
Call it with:

  [php symfony emailDRExportMairies|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $ids = ExportClient::getInstance()->findAll(acCouchdbClient::HYDRATE_JSON)->getIds();

    $exports = array();

    foreach($ids as $id) {
        $export = acCouchdbManager::getClient()->find($id, acCouchdbClient::HYDRATE_JSON);
        if ($export->destinataire == "Mairies") {
          $exports[$export->identifiant] = $export;
        }
    }

    $csv = array();
    $mairies = array();
    foreach (file(sfConfig::get('sf_data_dir') . '/import/maires.csv') as $c) {
        $csv = explode(';', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
        $mairies[$csv[0]] = $csv[1];
    }

    $nb_item = 0;
    $nb_email_send = 0;

    foreach($mairies as $insee => $email) {
      if (!array_key_exists($insee, $exports)) {
        $this->logSection('mairie not exist', $insee . ' : ' . $compte->getEmail());
        continue;
      }

      $export = $exports[$insee];

      $nb_item++;

      try {
        $message = $this->getMailer()->compose()
                ->setFrom(array('dominique@civa.fr' => "Webmaster Vinsalsace.pro"))
                ->setTo(trim($email))
                //->setTo('vlaurent.pro@gmail.com')
                ->setSubject('Déclarations de récolte 2011')
                ->setBody($this->getMessageBody($export));
        
        if ($options['send']) {        
          $sended = $this->getMailer()->send($message);
        } else {
          $sended = true;
        }

      } catch (Exception $exc) {
          $sended = false;
      }
      
      if ($sended) {
          $nb_email_send++;
          $this->logSection('sended', $insee . ' : ' . $email);
      } else {
          $this->logSection('send error', $insee . ' : ' . $email, null, 'ERROR');
      }
      unset($exports[$insee]);
    }


    $this->logSection('Emails have been sended', sprintf('%d / %d envoyés', $nb_email_send,  $nb_item));

    print_r(array_keys($exports));
  }

  protected function getMessageBody($export) {
      return "Monsieur le Maire
 
Comme l’an passé pour la plupart d’entre vous ou pour la première fois pour certains autres, nous mettons à votre disposition un lien qui vous permet d’accéder
à l’ensemble des Déclarations de Récolte (en version PDF) des récoltants qui dépendent de votre commune.
 
Contrairement à l’an passé ce lien est un lien permanent vous donnant accès, dans un espace totalement sécurisé, à votre dossier Déclaration de Récolte.
Celui-ci sera décliné par année de récolte et donc enrichi au fil des campagnes ; vous n’y trouverez pour l’instant que le dossier 2011 mais dans quelques jours également le dossier 2010.
 
https://declaration.vinsalsace.pro/mise_a_disposition/".$export->cle."/DR
 
D’autre part nous vous ferons également parvenir début 2012 la consolidation des surfaces et volumes par cépage déclarés dans votre commune pour l’ensemble des déclarants.
 
Nous profitons également de cet envoi pour vous souhaiter d’excellentes fêtes de fin d’année.
 
L’équipe du CIVA";
  }
}
