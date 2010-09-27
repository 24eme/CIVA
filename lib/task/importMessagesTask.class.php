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

	$json->msg_compte_index_intro = "(msg_compte_index_intro) Pour créer votre compte, merci d'indiquer votre numéro CVI et votre code de création de compte&nbsp;:";

        /** ERRORS **/
        $json->err_exploitation_acheteurs_popup_no_required = "(err_exploitation_acheteurs_popup_no_required) Veuillez cocher au moins une case pour continuer !";
        $json->err_exploitation_lieudits_popup_no_required = "(err_exploitation_lieudits_popup_no_required) Veuillez séléctionner au moins un lieu-dit !";
	$json->err_dr_popup_no_superficie = "(err_dr_popup_no_superficie) Vous n'avez pas saisi de superficie.";
	$json->err_dr_popup_min_quantite = "(err_dr_popup_min_quantite) Vous n'avez pas respecté le volume minimal";
        $json->err_dr_popup_max_quantite = "(err_dr_popup_max_quantite) Vous n'avez pas respecté le volume maximum";
        $json->err_dr_popup_dest_rebeches = "(err_dr_popup_dest_rebeches) Vous n'avez pas respecté la répartition des rebeches.";

        $json->err_dr_popup_unique_mention_denomination = "(err_dr_popup_unique_mention_denomination) La dénomination complémentaire et/ou la mention VT/SGN de chaque colonne doit être unique";
        $json->err_dr_popup_unique_denomination = "(err_dr_popup_unique_denomination) La dénomination complémentaire de chaque colonne doit être unique";

        $json->err_log_lieu_non_saisie = "(err_log_lieu_non_saisie) lieu non saisi";
        $json->err_log_cepage_non_saisie = "(err_log_cepage_non_saisie) cépage non saisi";
        $json->err_log_detail_non_saisie = "(err_log_detail_non_saisie) details non saisis";
        $json->err_log_ED_non_saisie = "(err_log_ED_non_saisie) Détails Edelzwicker non saisis, alors qu'il s'agit d'un motif de non récolte.";
        $json->err_log_cremant_pas_rebeches = "(err_log_cremant_pas_rebeches) pas de rebêches pour ce crémant";
        $json->err_log_cremant_min_quantite = "(err_log_cremant_min_quantite) Vous n'avez pas respecté le volume minimal";
        $json->err_log_cremant_max_quantite = "(err_log_cremant_max_quantite) Vous n'avez pas respecté le volume maximum";

        $json->err_log_superficie_zero = "(err_log_superficie_zero) Vous n'avez pas renseigné de detail pour cette appellation";
        $json->err_log_dplc = "(err_log_dplc) Votre DPLC cépage est important, sachez qu'il est possible de replier le volume.";


        /** HELP¨**/
        $json->help_popup_exploitation_administratif = "(help_popup_exploitation_administratif)Exploitation administratif : Message d'aide à définir.";
        $json->help_popup_exploitation_administratif_exploitation = "(help_popup_exploitation_administratif_exploitation) Exploitation : Message d'aide à définir.";
        $json->help_popup_exploitation_administratif_gestionnaire = "(help_popup_exploitation_administratif_gestionnaire) Gestionnnaire : Message d'aide à définir.";
        $json->help_popup_exploitation_administratif_siret = "(help_popup_exploitation_administratif_siret) Siret : Message d'aide à définir.";

        $json->help_popup_mon_espace_civa = "(help_popup_mon_espace_civa) Mon espace civa : Message d'aide à définir.";
        $json->help_popup_mon_espace_civa_ma_dr = "(help_popup_mon_espace_civa_ma_dr) Ma déclaration de récolte : Message d'aide à définir.";
        $json->help_popup_mon_espace_civa_visualiser = "(help_popup_mon_espace_civa_visualiser) Visualiser mes DR : Message d'aide à définir.";
        $json->help_popup_mon_espace_civa_gamma = "(help_popup_mon_espace_civa_gamma) Gamma : Message d'aide à définir.";

        $json->help_popup_exploitation_acheteur = "(help_popup_exploitation_acheteur) Exploitation acheteur : Message d'aide à définir.";
        $json->help_popup_exploitation_acheteur_vol_sur_place = "(help_popup_exploitation_acheteur_vol_sur_place) Vol sur place : Message d'aide à définir.";
        $json->help_popup_exploitation_acheteur_acheteurs_raisin = "(help_popup_exploitation_acheteur_acheteurs_raisin) Acheteurs : Message d'aide à définir.";
        $json->help_popup_exploitation_acheteur_caves_cooperatives = "(help_popup_exploitation_acheteur_caves_cooperatives) Caves : Message d'aide à définir.";
        $json->help_popup_exploitation_acheteur_acheteurs_mouts = "(help_popup_exploitation_acheteur_acheteurs_mouts) Mouts : Message d'aide à définir.";

        $json->help_popup_exploitation_lieu = "(help_popup_exploitation_lieu) Exploitation lieu : Message d'aide à définir.";

        $json->help_popup_DR = "(help_popup_DR) DR : Message d'aide à définir.";
        $json->help_popup_DR_denomination = "(help_popup_DR_denomination) Denomination : Message d'aide à définir.";
        $json->help_popup_DR_mention = "(help_popup_DR_mention) Mention : Message d'aide à définir.";
        $json->help_popup_DR_superficie = "(help_popup_DR_superficie) Superficie : Message d'aide à définir.";
        $json->help_popup_DR_vente_raisins = "(help_popup_DR_vente_raisins) Vente raisins : Message d'aide à définir.";
        $json->help_popup_DR_caves = "(help_popup_DR_caves) Cave : Message d'aide à définir.";
        $json->help_popup_DR_vol_place = "(help_popup_DR_vol_place) Vol sur place : Message d'aide à définir.";
        $json->help_popup_DR_vol_total_recolte = "(help_popup_DR_vol_total_recolte) Total récolte : Message d'aide à définir.";
        $json->help_popup_DR_vol_revendique = "(help_popup_DR_vol_revendique) Vol revendiqué : Message d'aide à définir.";
        $json->help_popup_DR_dplc = "(help_popup_DR_dplc) DPLC : Message d'aide à définir.";
        $json->help_popup_DR_total_cepage = "(help_popup_DR_total_cepage) Total cépage : Message d'aide à définir.";
        $json->help_popup_DR_total_appellation = "(help_popup_DR_total_appellation) Total appellation : Message d'aide à définir.";
        $json->help_popup_DR_recap_vente = "(help_popup_DR_recap_vente) Recap Vente : Message d'aide à définir.";

        $json->help_popup_autres = "(help_popup_autres) Autres : Message d'aide à définir.";
        $json->help_popup_autres_lies = "(help_popup_autres_lies) Lies : Message d'aide à définir.";
        $json->help_popup_autres_jv = "(help_popup_autres_jv) Jeunes vignes : Message d'aide à définir.";

        $json->help_popup_validation = "(help_popup_validation) Validation : Message d'aide à définir.";
        $json->help_popup_validation_log_erreur = "(help_popuplog_erreur) Erreurs : Message d'aide à définir.";
        $json->help_popup_validation_log_erreur = "(help_popup_validation_log_erreur) Erreurs : Message d'aide à définir.";
        $json->help_popup_validation_log_vigilance = "(help_popup_validation_log_vigilance) Vigilance : Message d'aide à définir.";

        /** INTRO **/
        $json->intro_mon_espace_civa_dr_validee = "(intro_mon_espace_civa_dr_validee) Vous avez deja validé votre déclaration de récolte.";
        $json->intro_gamma = "(intro_gamma) Lien vers votre espace Gamma";
        $json->intro_exploitation_administratif = "(intro_exploitation_administratif) Données administratives, n'hésitez pas à les modifier en cas de changement.";
        $json->intro_exploitation_acheteurs = "(intro_exploitation_acheteurs) Veuillez saisir les destinations de la récoltes.";
        $json->intro_exploitation_lieu = "(intro_exploitation_lieu) Indiquez les lieux-dits pour lesquels vous récoltez de l'AOC.";
        $json->intro_exploitation_lieu_txt_gris = "(intro_exploitation_lieu_txt_gris) Lieux-dits : ";
        $json->intro_declaration_recolte = "(intro_declaration_recolte) Pour chaque cépage de chaque appellation, veuillez saisir les informations demandées.";
        $json->intro_exploitation_autres = "(intro_exploitation_autres) ";
        $json->intro_validation = "(intro_validation) Veuillez vérifier les informations saisies avant de valider votre déclaration.";
        


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
