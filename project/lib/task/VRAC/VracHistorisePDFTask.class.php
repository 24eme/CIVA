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

    $this->addArguments(array(
        new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'id'),
    ));

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

    $vrac = VracClient::getInstance()->find($arguments['id']);
    $vrac->historisePDF();
    $vrac->save();
    $this->logSection('update', $arguments['id'] . ' historisation du PDF avec succès');
  }
}
