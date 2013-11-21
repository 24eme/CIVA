<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class emailDSValidationtask
 * @author mathurin
 */
class VracUpdateNumCourtierTask extends sfBaseTask {
    
  protected function configure()
  {

    $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),

    ));

    $this->namespace        = 'vrac';
    $this->name             = 'update-num-courtier';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [EmailValidationVrac|INFO] task does things.
Call it with:

  [php symfony vrac:update-num-courtier|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    set_time_limit('240');
    ini_set('memory_limit', '512M');
    
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $contrats = VracContratsView::getInstance()->findAll();
    foreach ($contrats as $contrat) {
    	if ($contratObject = VracClient::getInstance()->find($contrat->id)) {
    		$contratObject->mandataire->num_db2 = null;
    		if ($contratObject->mandataire_identifiant) {
    			if ($courtier = _TiersClient::getInstance()->find($contratObject->mandataire_identifiant)) {
    				$contratObject->mandataire->num_db2 = $courtier->db2->num;
    			}
    		}
    		$contratObject->save();
    		$this->logSection('update', $contratObject->_id . ' mis à jour avec succès');
    	}
    }
  }
}
