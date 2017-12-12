<?php

class DRCreationFromAcheteur extends sfBaseTask {

    protected function configure() {
      $this->addOptions(array(
			      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
			      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
			      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
			      new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'couchdb'),
			      new sfCommandOption('removedb', null, sfCommandOption::PARAMETER_REQUIRED, '= yes if remove the db debore import [yes|no]', 'no'),
			      new sfCommandOption('cvi', null, sfCommandOption::PARAMETER_OPTIONAL, 'CVI du récoltant dont il faut générer une DR', ''),
			      new sfCommandOption('save-error', null, sfCommandOption::PARAMETER_OPTIONAL, 'Sauve la DR même si les données importée sont en erreur', ''),
			      new sfCommandOption('save-vigilance', null, sfCommandOption::PARAMETER_OPTIONAL, 'Sauve la DR même si les données importée sont en vigileance', ''),
			      new sfCommandOption('year', null, sfCommandOption::PARAMETER_REQUIRED, 'Année de la DR à générer', ''),
                  new sfCommandOption('dryrun', null, sfCommandOption::PARAMETER_REQUIRED, 'Si true ne sauvegarde pas', false)
			      ));

      $this->namespace = 'DR';
      $this->name = 'creationFromAcheteur';
      $this->briefDescription = '';
      $this->detailedDescription = <<<EOF
	Generate DR
EOF;
    }

    protected function createOne($cvi, $year) {
	$e = EtablissementClient::getInstance()->find("ETABLISSEMENT-".$cvi);
	if (!$e) {
	  print "ERROR;;".$cvi.";;;".$e->getMessage()."\n";
	  return false;
	}
	$dr = DRClient::getInstance()->retrieveByCampagneAndCvi($cvi, $year);
	if ($dr) {
	  print "EXISTE;;".$cvi."\n";
	  return false;
	}
	$import_from = array();
	try{
      if (!$dr)
 	    $dr = DRClient::getInstance()->createFromCSVRecoltant($year, $e, $import_from);
	  $check = $dr->check();
      $acheteurs = array_merge($dr->acheteurs->getArrayNegoces(), $dr->acheteurs->getArrayCooperatives(), $dr->acheteurs->getArrayMouts());
	  if (count($check['erreur']) || count($check['vigilance'])) {
        if (count($check['erreur']) > 0) {
            foreach($check['erreur'] as $err) {
	            print "ERROR;".$dr->_id.";".$dr->cvi.";".$dr->declarant->nom.";".implode(", ", $acheteurs).";" .$err['info'] . ";" . $err['log']. "\n";
            }
        }

        if (count($check['vigilance']) > 0) {
            foreach($check['vigilance'] as $err) {
                print "VIGILANCE;".$dr->_id.";".$dr->cvi.";".$dr->declarant->nom.";".implode(", ", $acheteurs).";" .$err['info'] . ";" . $err['log']. "\n";
            }

        }

        if (count($check['vigilance']) && !$this->save_vigilance)
	      return false;
        else
           $dr->validate($year."-12-12", "COMPTE-auto");
	    if (count($check['erreur']) && !$this->save_error)
		return false;

	  }else{
          $e = EtablissementClient::getInstance()->find("ETABLISSEMENT-".$dr->getCVI());
	    if (!$e) {
	      print "ERROR: unknown tiers".$dr->getCVI()."\n";
	      return false;
	    }
	    $dr->validate($year."-12-12", "COMPTE-auto");
	  }
	}catch(sfException $e) {
	  print "ERROR;".$dr->_id.";".$dr->cvi.";".$dr->declarant->nom.";;".$e->getMessage()."\n";
	  return false;
	}
	try {
        if(!$this->dryrun) {
	       $dr->save();
        }
	}catch(sfException $e) {
	  print "ERROR;".$dr->_id.";".$dr->cvi.";".$dr->declarant->nom.";;".$e->getMessage()."\n";
	  return false;
	}
	print "CREEE;".$dr->_id.";".$dr->cvi.";".$dr->declarant->nom."\n";
	return true;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        $routing = clone ProjectConfiguration::getAppRouting();
        $context = sfContext::createInstance($this->configuration);
        $context->set('routing', $routing);
	   $this->save_error = $options['save-error'];
	   $this->save_vigilance = $options['save-vigilance'] || $options['save-error'];
       $this->dryrun = $options['dryrun'];

	$campagne = $options['year'];

	if ($options['cvi'] && $campagne) {
	  return $this->createOne($options['cvi'], $campagne);
	}

	if (!$options['year']) {
	  print "ERROR : options --year needed\n";
	  return ;
	}

    print "type;ID doc;cvi récoltant;nom récoltant;cvi(s) acheteur(s);erreur;detail de l'erreur\n";

	$CVIs = acCouchdbManager::getClient()->startkey(array((string)$campagne))->endkey(array((string)($campagne+1)))->getView("CSV", "recoltant");

	foreach ($CVIs->rows as $o) {
	  $this->createOne($o->key[1], $campagne);
	}
    }

  }
