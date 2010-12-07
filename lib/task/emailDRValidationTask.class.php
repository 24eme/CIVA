<?php

class emailDRValidationTask extends sfBaseTask
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
      new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, "Année de déclaration", '2010'),
    ));

    $this->namespace        = 'email';
    $this->name             = 'dr-validation';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [EmailValidationDR|INFO] task does things.
Call it with:

  [php symfony EmailValidationDR|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    set_time_limit('240');
    ini_set('memory_limit', '512M');
    
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $nb_item = 0;
    $nb_email_send = 0;
    $tiers = sfCouchdbManager::getClient("Tiers")->getAll(sfCouchdbClient::HYDRATE_JSON);
    foreach ($tiers as $item) {
        $dr = sfCouchdbManager::getClient("DR")->retrieveByCampagneAndCvi($item->cvi, $options['campagne'], sfCouchdbClient::HYDRATE_JSON);
        if ($dr && (!isset($dr->validee) || !$dr->validee)) {
            $nb_item++;
            if(!$item->email) {
                $this->logSection('no email', $item->cvi, null, 'ERROR');
                continue;
            }
            $message = $this->getMailer()->compose()
                      ->setFrom(array('dominique@civa.fr' => "Webmaster Vinsalsace.pro"))
                      //->setTo($item->email)
                      ->setTo('vince.laurent@gmail.com')
                      ->setSubject('RAPPEL DR 2010')
                      ->setBody($this->getMessageBody($item));
            try {
                $sended = $this->getMailer()->send($message);
            } catch (Exception $exc) {
                $sended = false;
            }
            
            if ($sended) {
                $nb_email_send++;
                $this->logSection('sended', $item->cvi . ' : ' . $item->email);
            } else {
                $this->logSection('send error', $item->cvi . ' : ' . $item->email, null, 'ERROR');
            }
        }
    }

    $this->logSection('Emails have been sended', sprintf('%d / %d envoyés', $nb_email_send,  $nb_item));
  }

  protected function getMessageBody($tiers) {
      return "Bonjour ".$tiers->nom.",

Vous avez commencé à saisir en ligne votre Déclaration de Récolte 2010.
Nous vous rappelons que la date limite de validation est le 10 Décembre.

Cordialement,

Le CIVA";
  }
}
