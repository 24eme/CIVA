<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class emailDSValidationtask
 * @author mathurin
 */
class VracHistorisePDFTask extends sfBaseTask {

  protected function configure()
  {

    $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),

    ));

    $this->namespace        = 'vrac';
    $this->name             = 'historise-pdf';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [VracHistorisePDF|INFO] task does things.
Call it with:

  [php symfony vrac:historise-pdf|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    sfContext::createInstance($this->configuration);

    $contrats = VracTousView::getInstance()->findAll();
    foreach ($contrats as $contrat) {
    	if ($contratObject = VracClient::getInstance()->find($contrat->id)) {
    		$contratObject->historisePDF();
    		$contratObject->save();
    		$this->logSection('update', $contratObject->_id . ' historisation du PDF avec succès');
    	}
    }
  }
}
