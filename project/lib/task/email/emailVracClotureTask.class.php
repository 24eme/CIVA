<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class emailDSValidationtask
 * @author mathurin
 */
class emailVracClotureTask extends sfBaseTask {
    
  protected function configure()
  {

    $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),

    ));

    $this->namespace        = 'email';
    $this->name             = 'vrac-cloture';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [EmailValidationVrac|INFO] task does things.
Call it with:

  [php symfony email:vrac-validation|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    set_time_limit('240');
    ini_set('memory_limit', '512M');
    
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
	  $routing = clone ProjectConfiguration::getAppRouting();
    $contextInstance = sfContext::createInstance($this->configuration);
    $contextInstance->set('routing', $routing);

  	$contrats = VracMailingView::getInstance()->getContratsForEmailCloture();
    foreach ($contrats as $contrat) {
        foreach(VracMailer::getInstance()->clotureContrat($contrat) as $message) {
            sfContext::getInstance()->getMailer()->send($message);
        }

		$this->logSection('sended', $contrat->_id);

		$contrat->valide->email_cloture = date('Y-m-d');
		$contrat->save();
    }
  }
}
