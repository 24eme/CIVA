<?php

class importMessagesTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
            new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'couchdb'),
            new sfCommandOption('removedb', null, sfCommandOption::PARAMETER_REQUIRED, '= yes if remove the db debore import [yes|no]', 'no'),
            new sfCommandOption('year', null, sfCommandOption::PARAMETER_REQUIRED, 'year version of the file to be imported', '09'),
        ));

        $this->namespace = 'import';
        $this->name = 'Messages';
        $this->briefDescription = 'import csv achat file';
        $this->detailedDescription = <<<EOF
The [import|INFO] task does things.
Call it with:

  [php symfony import|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        set_time_limit('3600');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
		
	if($options['removedb'] == 'yes' && $options['import'] == 'couchdb') {
	  if (sfCouchdbManager::getClient()->databaseExists()) {
	    sfCouchdbManager::getClient()->deleteDatabase();
	  }
	  sfCouchdbManager::getClient()->createDatabase();
	}


	$docs = array();

	$json = new stdClass();
	$json->_id = 'MESSAGES';
	$json->type = 'Messages';

	$json->msg_compte_index_intro = "Pour créer votre compte, merci d'indiquer votre numéro CVI et votre code de création de compte&nbsp;:";
	$json->err_dr_popup_no_superficie = "Vous n'avez pas saisi de superficie.";
	$json->err_dr_popup_min_quantite = "Vous n'avez pas respecté le volume minimal";
        $json->err_dr_popup_unique_mention_denomination = "La dénomination complémentaire et/ou la mention VT/SGN de chaque colonne doit être unique";

        $json->err_log_lieu_non_saisie = "lieu non saisi";
        $json->err_log_cepage_non_saisie = "cépage non saisi";
        $json->err_log_detail_non_saisie = "details non saisis";
        $json->err_log_cremant_pas_rebeches = "pas de rebêches pour ce crémant";
        $json->err_log_cremant_min_quantite = "Vous n'avez pas respecté le volume minimal";

	$docs[] = $json;

	if ($options['import'] == 'couchdb') {
	  foreach ($docs as $data) {
	    $doc = sfCouchdbManager::getClient()->retrieveDocumentById($data->_id);
	    if ($doc) {
	      $doc->delete();
	    }
            $doc = sfCouchdbManager::getClient()->createDocumentFromData($data);
	    $doc->save();
	  }
	  return;
	}
	echo '{"docs":';
	echo json_encode($docs);
	echo '}';
    }

}
