<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class emailDSValidationtask
 * @author mathurin
 */
class emailDSValidationTask extends sfBaseTask {
    
  protected function configure()
  {
    $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Année de déclaration'),
    ));

    $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('dsid', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', ''),

    ));

    $this->namespace        = 'email';
    $this->name             = 'ds-validation';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [EmailValidationDS|INFO] task does things.
Call it with:

  [php symfony EmailValidationDS|INFO]
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
            $campagne = $arguments['campagne'];
            $exportManager = new ExportDSCiva($campagne);
            if ($options['dsid']) {
                $dss = array(DSCivaClient::getInstance()->find($options['dsid']));
            }else{
                $dss = $exportManager->getDSNonValideesListe();
            }
	    foreach ($dss as $ds) {
		$cvi = $ds->declarant->get('cvi');

                $nb_item++;
                if(!$ds->declarant->exist('email')) {
                    $this->logSection('no email', $cvi, null, 'ERROR');
                continue;
                }
            
                try {
            	$message = $this->getMailer()->compose()
                      ->setFrom(array('dominique@civa.fr' => "Dominique Wolff"))
                      ->setTo($ds->declarant->get('email'))
                     // ->setTo('mpetit@actualys.com')
                      ->setSubject('RAPPEL DS '.$arguments['campagne'])
                      ->setBody($this->getMessageBody($ds->declarant->get('nom'), $arguments['campagne']));
                $sended = $this->getMailer()->send($message);
                
                //echo $this->getMessageBody($compte, $arguments['campagne'])."\n\n\n";
            } catch (Exception $exc) {     
                $this->logSection('send error', $cvi . ' : ' . $ds->declarant->get('email'), null, 'ERROR');  
                $this->logSection('send error', $cvi . ' : ' . $exc->getMessage());    
                $sended = false;
            }
            
            if ($sended) {
                $nb_email_send++;
                $this->logSection('sended', $cvi . ' : ' . $ds->declarant->get('email'));
            }           
        }
        $this->logSection('Emails have been sended', sprintf('%d / %d envoyés', $nb_email_send,  $nb_item));
    }
  }

  protected function getMessageBody($nom, $campagne) {
      return "Bonjour,

Vous avez commencé à saisir en ligne votre Déclaration de Stocks ".$campagne." sur le site VinsAlsace.pro et ne l’avez pas encore validée. 

Nous vous rappelons que vous devez impérativement la valider avant le 31 août minuit. 

Pour terminer la saisie, cliquez sur le lien suivant :
http://declaration.vinsalsace.pro/

Si vous avez déposé en mairie une déclaration papier, merci de m'en informer par retour de mail.

Bien cordialement,

Dominique Wolff
--
Responsable Informatique
Tél: 03.89.20.16.20
Fax: 03.89.20.16.30
dominique@civa.fr
http://www.vinsalsace.com
";
  }
}
