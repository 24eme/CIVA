<?php

class setTiersInfosTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      new sfCommandOption('file', null, sfCommandOption::PARAMETER_REQUIRED, 'import from file', sfConfig::get('sf_data_dir') . '/import/Tiers'),
    ));

    $this->namespace        = 'civa-config';
    $this->name             = 'setTiersInfos';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [importTiersInfos|INFO] task does things.
Call it with:

  [php symfony importTiersInfos|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '512M');
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $docs = sfCouchdbManager::getClient('Tiers')->getAll();

    $csv = array();
    $csv_cvi_no_stock = array();
    foreach (file($options['file']) as $a) {
        $tiers = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $a)));
        for($i = 0 ; $i < count($tiers) ; $i++) {
            if (!isset($csv[$tiers[3]][$i]) || !$csv[$tiers[3]][$i])
              $csv[$tiers[3]][$i] = $tiers[$i];
            else if ($tiers[$i] && !$tiers[1]) {
              $csv[$tiers[3]][$i] = $tiers[$i];
            }
            if ($tiers[1] && $i == 57)
	      $csv[$tiers[3]][99] = $tiers[57];
        }
    }

    foreach($docs as $doc) {
        $tiers = $csv[$doc->no_stock];

        $doc->maison_mere = $tiers[10];
	$doc->civaba = $tiers[1];
        $doc->no_accises = $tiers[70];
        $doc->intitule = $tiers[9];

        if (isset($tiers[99]))
	    $doc->cvi_acheteur = $tiers[99];
        if ($tiers[37])
	  $doc->telephone = sprintf('%010d', $tiers[37]);
        if ($tiers[39])
	  $doc->fax = sprintf('%010d', $tiers[39]);
        if($tiers[40])
	  $doc->email = $tiers[40];
        if(isset($tiers[82]))
	  $doc->web = $tiers[82];

          $doc->exploitant->sexe = $tiers[41];
	  $doc->exploitant->nom = $tiers[42];
	  if ($tiers[13]) {
	    $doc->exploitant->adresse = $tiers[12].", ".$tiers[13];
	    $doc->exploitant->code_postal = $tiers[15];
	    $doc->exploitant->commune = $tiers[14];
	  }
	  if ($tiers[25]) {
	    $doc->exploitant->telephone = sprintf('%010d', $tiers[38]);
          }
	  $doc->exploitant->date_naissance = sprintf("%04d-%02d-%02d", $tiers[8], $tiers[69], $tiers[68]);
        

        $doc->save();
        $this->log($doc->cvi);
    }
  }
}
