<?php

class DRCreationFromAcheteur extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('cvi', null, sfCommandOption::PARAMETER_REQUIRED, 'CVI du récoltant dont il faut générer une DR'),
            new sfCommandOption('year', null, sfCommandOption::PARAMETER_REQUIRED, 'Année de la DR à générer'),
	));

        $this->namespace = 'DR';
        $this->name = 'creationFromAcheteur';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
Generate DR
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '1024M');
        set_time_limit('3600');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

	$dr = sfCouchdbManager::getClient('DR')->retrieveByCampagneAndCvi($option['cvi'], $option['year']);
	if ($dr) {
	  print "LOG : ".$option['cvi'].' existes\n';
	  return ;
	}

	$import_from = array();
	$dr = sfCouchdbManager::getClient('DR')->createFromCSVRecoltant($tiers, $import_from);
	$check = $dr->check();
	if (count($check['erreur']) || count($check['vigilance'])) {
	  print "ERROR: ".$dr->id." a des erreurs ou des points de vigilance\n";
	}else{
	  $tiers = sfCouchdbManager::getClient('Recoltant')->retrieveByCvi($dr->getCVI());
	  $dr->validate($tiers);
	}
	$dr->save();
    }

  }
