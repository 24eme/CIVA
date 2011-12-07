<?php

class emailDRValidationTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));
	$this->addArguments(array(
		new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Année de déclaration'),
	));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      //new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, "Année de déclaration", '2010'),
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

    if (isset($arguments['campagne']) && !empty($arguments['campagne'])) {
	    $nb_item = 0;
	    $nb_email_send = 0;
	    $tiers = sfCouchdbManager::getClient()->getView("DR", "non_validees");
	    foreach ($tiers->rows as $item) {
	    	$this->logSection('cvi', $item->value->declarant->email);
	    	
	    	
            $nb_item++;
            if(!$item->value->declarant->email) {
                $this->logSection('no email', $item->value->cvi, null, 'ERROR');
                continue;
            }
            $message = $this->getMailer()->compose()
                      ->setFrom(array('dominique@civa.fr' => "Webmaster Vinsalsace.pro"))
                      ->setTo($item->value->declarant->email)
                      //->setTo('vince.laurent@gmail.com')
                      ->setSubject('RAPPEL DR '.$arguments['campagne'])
                      ->setBody($this->getMessageBody($item->value->declarant, $arguments['campagne']));
            try {
                $sended = true;//$this->getMailer()->send($message);
            } catch (Exception $exc) {
                $sended = false;
            }
            
            if ($sended) {
                $nb_email_send++;
                $this->logSection('sended', $item->value->cvi . ' : ' . $item->value->declarant->email);
            } else {
                $this->logSection('send error', $item->value->cvi . ' : ' . $item->value->declarant->email, null, 'ERROR');
            }
            
            
	    }
	    $this->logSection('Emails have been sended', sprintf('%d / %d envoyés', $nb_email_send,  $nb_item));
    }
  }

  protected function getMessageBody($tiers, $campagne) {
      return "Bonjour ".$tiers->nom.",

Vous avez commencé à saisir en ligne votre Déclaration de Récolte ".$campagne." sur le site VinsAlsace.pro et ne l’avez pas encore validé.
Nous vous rappelons que vous devez impérativement la valider avant le 10 décembre minuit.

Si vous avez déposé une Déclaration de Récolte 'papier' veuillez SVP la supprimer sur le site.

Cordialement,

Le CIVA";
  }
}
