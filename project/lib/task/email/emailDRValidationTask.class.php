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
	    $drs = sfCouchdbManager::getClient()->startkey(array($arguments['campagne']))
		    				->endkey(array($arguments['campagne'], array()))
						->getView("DR", "non_validees");
	    foreach ($drs->rows as $item) {
		$cvi = $item->key[1];

	    	$this->logSection('cvi', $cvi);
	    	
	    	$compte = sfCouchdbManager::getClient()->retrieveDocumentById('COMPTE-'.$cvi);
	    	
            $nb_item++;
            if(!$compte->getEmail()) {
                $this->logSection('no email', $cvi, null, 'ERROR');
                continue;
            }
            
            try {
            	$message = $this->getMailer()->compose()
                      ->setFrom(array('dominique@civa.fr' => "Webmaster Vinsalsace.pro"))
                      ->setTo($compte->getEmail())
                      //->setTo('vince.laurent@gmail.com')
                      ->setSubject('RAPPEL DR '.$arguments['campagne'])
                      ->setBody($this->getMessageBody($compte, $arguments['campagne']));
                $sended = $this->getMailer()->send($message);
                
                //echo $this->getMessageBody($compte, $arguments['campagne'])."\n\n\n";
            } catch (Exception $exc) {
                $sended = false;
            }
            
            if ($sended) {
                $nb_email_send++;
                $this->logSection('sended', $cvi . ' : ' . $compte->getEmail());
            } else {
                $this->logSection('send error', $cvi . ' : ' . $compte->getEmail(), null, 'ERROR');
            }
            
            
	    }
	    $this->logSection('Emails have been sended', sprintf('%d / %d envoyés', $nb_email_send,  $nb_item));
    }
  }

  protected function getMessageBody($compte, $campagne) {
      return "Bonjour ".$compte->getNom().",

Vous avez commencé à saisir en ligne votre Déclaration de Récolte ".$campagne." sur le site VinsAlsace.pro et ne l’avez pas encore validé. 

Nous vous rappelons que vous devez impérativement la valider avant ce soir minuit. 

Cordialement,

Le CIVA";
  }
}
