<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class emailDSValidationtask
 * @author mathurin
 */
class VracExpirationTask extends sfBaseTask {
    
  protected function configure()
  {

    $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('dsid', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', ''),

    ));

    $this->namespace        = 'vrac';
    $this->name             = 'expiration';
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
	$delai = sfConfig::get('app_configuration_vrac');
	$delai = $delai['delai_suppression'];
  	$contrats = VracMailingView::getInstance()->getContratsExpires($delai);
    foreach ($contrats as $contrat) {		
		$acteurs = $contrat->getActeurs();
		$contrat->motif_suppression = "Non signature dans un délai de 5 jours";
		$contrat->valide->statut = Vrac::STATUT_ANNULE;
		$contrat->save();
		foreach ($acteurs as $type => $acteur) {
			VracMailer::getInstance()->annulationContrat($contrat, $acteur->email);
			$this->logSection('sended', $contrat->_id . ' => ' . $acteur->raison_sociale);
		}
    }
  }
}
