<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class emailDSValidationtask
 * @author mathurin
 */
class emailVracValidationTask extends sfBaseTask {
    
  protected function configure()
  {

    $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),

    ));

    $this->namespace        = 'email';
    $this->name             = 'vrac-validation';
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
	$contextInstance = sfContext::createInstance($this->configuration);
  	$contrats = VracMailingView::getInstance()->getContratsForEmailValide();
    foreach ($contrats as $contrat) {
    	$document = new ExportVracPdf($contrat, array($contextInstance->getController()->getAction('vrac_export', 'main'), 'getPartial'));
    	$document->generatePDF();
    	$filePath = sfConfig::get('sf_cache_dir').'/pdf/'.$document->getFileName(true, true);
    	$acteurs = $contrat->getActeurs();
		foreach ($acteurs as $type => $acteur) {
      foreach($acteur->emails as $email) {
			    VracMailer::getInstance()->validationContrat($contrat, $email, $filePath);
			    $this->logSection('sended', $contrat->_id . ' => ' . $acteur->raison_sociale);
      }
		}
		$contrat->valide->email_validation = date('Y-m-d');
		$contrat->save();
    }
  }
}
