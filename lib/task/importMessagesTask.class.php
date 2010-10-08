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

	$json->msg_compte_index_intro = "Pour créer votre compte, merci d'indiquer votre numéro CVI et votre code de création de compte (ceux que le CIVA vous a communiqués par courrier de compte)";

        /** ERRORS **/
        $json->err_exploitation_acheteurs_popup_no_required = "Veuillez cocher au moins une case pour continuer !";
        $json->err_exploitation_lieudits_popup_no_required = "Veuillez séléctionner au moins un lieu-dit !";
	$json->err_dr_popup_no_superficie = "Vous n'avez pas saisi de superficie. La surface est obligatoire sauf dans l'Edel";
	$json->err_dr_popup_min_quantite = "Les rebêches doivent représenter au minimum 2 % du volume total produit";
        $json->err_dr_popup_max_quantite = "Les rebêches doivent représenter au maximum 10% du volume total produit";
        $json->err_dr_popup_dest_rebeches = "Vous n'avez pas respecté la répartition des rebêches.";

        $json->err_dr_popup_unique_mention_denomination = "Il faut distinguer la colonne par une mention complémentaire ou par une mention VT/SGN.";
        $json->err_dr_popup_unique_denomination = "Il faut distinguer la colonne par une mention complémentaire ou par une mention VT/SGN.";

        $json->err_dr_recap_vente_popup_superficie_trop_eleve = " La somme des superficies des acheteurs ne peut pas être superieure au total de l'appellation";
        $json->err_dr_recap_vente_popup_dplc_trop_eleve = "La somme des DPLC des acheteurs ne peut pas être superieure au DPLC total de l'appellation";

        $json->err_log_lieu_non_saisie = "Llieu non saisi";
        $json->err_log_cepage_non_saisie = "Cépage non saisi";
        $json->err_log_detail_non_saisie = "Details non saisis";
        $json->err_log_ED_non_saisie = "Vous nous avez indiqué comme motif de non récolte \"assemblage Edel\" mais vous n'avez pas saisi d'Edel";
        $json->err_log_cremant_pas_rebeches = "Vous avez oublié de saisir les rebêches";
        $json->err_log_cremant_min_quantite = "Les rebêches doivent représenter au minimum 2% du volume total produit";
        $json->err_log_cremant_max_quantite = "Les rebêches doivent représenter au maximum 10% des volumes produits";

        $json->err_log_superficie_zero = "Vous n'avez pas renseigné de detail pour cette appellation";
        $json->err_log_dplc = "err_log_dplc";


        /** HELP¨**/
        $json->help_popup_exploitation_administratif = "Identification de l'exploitation : les renseignements affichés correspondent aux données que nous connaissons vous concernant. Vous pouvez procéder à des modifications, mais elles ne seront définitivement prises en compte, qu'après validation des Services de la Viticulture de la DGDDI (Douanes). ";
        $json->help_popup_exploitation_administratif_exploitation = "Nom déclaré auprès du Service de la viticulture (nom de l'exploitant ou nom déclaré de la Société)";
        $json->help_popup_exploitation_administratif_gestionnaire = "Nom de la personne désignée pour être l'interlocutrice de l'Administration";
        $json->help_popup_exploitation_administratif_siret = "N° SIRET obligatoire pour les SA, SARL, SCA, GAEC, EARL…";

        $json->help_popup_mon_espace_civa = "Vous êtes ici sur votre espace personnel totalement sécurisé . Vous pouvez y effectuez en toute sécurité et en toute confidentialité votre déclaration de récolte et consulter celles des années précédentes. Si vous utilisez des DAA/DSA vous pouvez également ici accéder à l'espace test du téléservice \"AlsaceGamm@\". Pour tout renseignement concernant cet espace vous pouvez appeler au CIVA 03 89 20 16 20 demander Dominique WOLFFou Béatrice FISCHER";
        $json->help_popup_mon_espace_civa_ma_dr = "Vous pouvez saisir votre déclaration de récolte, soit à partir d' une déclaration totalement vierge, soit à partir de la déclaration d'une année précédente préremplie des surfaces (cette  option n'effacera en aucun cas les données de l'année sélectionnée qui seront toujours conservées). Une fois que vous avez commencez à saisir, vous  pouvez à tout moment supprimer la déclaration en cours, et recommencer.";
        $json->help_popup_mon_espace_civa_visualiser = "Vous pouvez ici uniquement visualiser vos déclarations des années précédentes. Lorsque vous sélectionnerez une année, le logiciel générera un fichier au format pdf que vous pourrez consulter à l'écran ou imprimer.";
        $json->help_popup_mon_espace_civa_gamma = "Choisir cette option pour accéder à l'environnement TEST du  téléservice \"AlsaceGamm@\". Vous pourrez tester l'application et vous déterminez ensuite pour l'utilisation de Gamma soit sur ce portail soit sur celui des Douanes. Il ne s'agit ici que d'un environnement TEST pour vous permettre de vous familiariser avec l'application. En aucun cas vous ne pouvez à ce stade transmettre des DAE en temps réel. voir aussi le courrier qui vous a été envoyé par le CIVA le 18 octobre 2010.";

        $json->help_popup_exploitation_acheteur = "Vous renseignez ici la répartition de votre récolte par appellation. Vous identifiez également ici vos acheteurs de raisins et/ou les caves coopératives auprès desquelles vous êtes adhérent. Les renseignements de cet écran sont très importants. En effet, les écrans suivants seront générés en fonction de ce que vous cocherez ici. Cependant en cas d'oubli à ce stade, vous pourrez toujours rajouter un acheteur ou une appellation dans les étapes suivantes.";
        $json->help_popup_exploitation_acheteur_vol_sur_place = "Vous cochez ici les appellations pour le volume de récolte que vous avez gardé sur place (chez vous). Vous pouvez facilement cocher ou décocher les cases.";
        $json->help_popup_exploitation_acheteur_acheteurs_raisin = "Vous sélectionnez ici vos acheteurs et vous renseignez les appellations pour chacun d'eux.  Pour sélectionner un acheteur de raisins, cliquez sur le bouton vert à droite \"AJOUTER UN ACHETEUR\". Puis à gauche dans la zone de saisie, vous tapez  les premières lettres du nom de l'acheteur, ou du village ou le n° CVI. Une liste déroulante apparaitra. Vous pourrez sélectionner l'acheteur concerné et cocher les appellations qui correspondent à votre livraison. Puis cliquez sur le bouton vert \"VALIDEZ\". En cours de saisie, si vous vous êtes trompé vous pouvez à tout moment annuler (bouton rouge ANNULER ) . . Si votre acheteur n'est pas présent dans la liste vous pouvez nous appeler au 03 89 20 16 20 demander Dominique WOLFF ou Béatrice FISCHER";
        $json->help_popup_exploitation_acheteur_caves_cooperatives = "Vous sélectionnez ici la ou les caves coopératives auprès desquelles vous êtes adhérent et vous renseignez les appellations pour chacune d'elle. Pour sélectionner une Cave, cliquez sur le bouton vert à droite \"AJOUTER UNE CAVE\". Puis à gauche dans la zone de saisie, vous tapez  les premières lettres du nom de cave ou la commune ou le n° CVI. Une liste déroulante apparaitra. Vous pourrez sélectionner la Cave Coopérative concernée et cocher les appellations qui correspondent à votre livraison. Puis cliquez sur le bouton vert \"VALIDEZ\". En cours de saisie, si vous vous êtes trompé vous pouvez à tout moment annuler (bouton rouge ANNULER ) . Une fois que vous avez validé la cave  si vous voulez la supprimer, cliquez sur la croix rouge ";
        $json->help_popup_exploitation_acheteur_acheteurs_mouts = "Si vous avez vendu des môuts destinés à l'élaboration de Crémant d' Alsace vous renseignez cette zone. Vous sélectionnez l'acheteur (négociant ou cave coopérative). Pour sélectionner un acheteur, cliquez sur le bouton vert à droite \"AJOUTER UN ACHETEUR\". Puis à gauche dans la zone de saisie, vous tapez  les premières lettres du nom ou la commune ou le n° CVI. Une liste déroulante apparaitra. Vous pourrez sélectionner le négociant ou la Cave Coopérative. Ici vous n'avez pas besoin de cocher l'appellation (l'appellation Crémant sera coché par défaut).  Puis cliquez sur le bouton vert \"VALIDEZ\". En cours de saisie, si vous vous êtes trompé vous pouvez à tout moment annuler (bouton rouge ANNULER ) ou supprimer (croix rouge) .Attention si vous vendez des moûts de Crémant d'ALSACE, la rebêche restera chez vous. ";

        $json->help_popup_exploitation_lieu = "Dans l'écran précédent vous avez cochez du Grand cru. Vous devez ici sélectionnez les lieux dits";

        $json->help_popup_DR = "Vous saisissez maintenant ici les surfaces et les volumes par cépage dans les différentes appellations que vous avez sélectionnées dans l'écran précédent. En cas d'oubli vous pouvez à tout moment rajouter une appellation et un acheteur ou une cave coopérative.";
        $json->help_popup_DR_denomination = "Dénomination complémentaire revendiquée. Préciser les lieux-dits revendiqués. Indiquer également dans cette rubrique les \"replis\" et les \"vins sous charte\", par ex. Edel Gentil s'il fait l'objet d'un assemblage avant vinification.";
        $json->help_popup_DR_mention = "A renseigner si vous avez récolté des Vendanges Tardives ou des Sélections de Grains Nobles . Sélectionner \"VT\" pour vendanges Tardives ou \"SGN\" pour Sélections de Grains Nobles. Attention toutes les parcelles en production doivent obligatoirement être récoltées à la date de souscription de la déclaration de récolte. Seules les Vendanges Tardives et Sélections de Grains Nobles ayant fait l'objet d'une déclaration préalable pourront déroger à cette obligation. SI c'est le cas vous devez indiquer la surface correspondante avec un volume estimatif (saisir \"estimation\" dans la rubrique \"dénomination complémentaire\". Une déclaration de récolte rectificative devra ensuite être effectuée auprès des Services des Douanes qui transmettront automatiquement une copie au CIVA. Pour cela il vous faut imprimer votre déclaration sous format pdf auquel vous pouvez accéder en utilisant le bouton \"prévisualiser\".Il est important également que vous rectifiiez votre DRM en conséquence. ";
        $json->help_popup_DR_superficie = "La surface est obligatoire (ares,ca). Les surfaces déclarées doivent obligatoirement correspondre au relevé parcellaire enregistré dans votre casier viticole (fiche de compte du CVI). Cas particulier l'Edelzwicker :  vous laissez  la surface dans le cépage d'origine sauf pour les parcelles complantées en cépages en mélange. Il est donc possible dans un cépage d'indiquer une surface sans volume si ce dernier est déclaré en Edelzwicker.  Pour l'edel \"repli butoir\" vous n'indiquez pas de surface (à laisser dans le cépage d'origine). Edel \"repli pinot noir\" : la surface obligatoire";
        $json->help_popup_DR_vente_raisins = "Vous indiquez ici pour chacun des acheteurs que vous aviez sélectionnés lors de l'étape précédente, le volume (hectolitres/litres) correspondant aux quantités de raisins livrés. Si votre acheteur vous communique les quantités en kilos, vous avez l'obligation de les convertir en hectolitres selon les coefficients forfaitaires suivants : AOC Alsace et Alsace Grand Cru (130 kgs = 1 Hl). AOC Crémant d'Alsace (150 kgs = 1 Hl). Si vous avez oublié de sélectionner un acheteur, vous pouvez le rajouter ici en cliquant sur \" + acheteur\". Saisir les premiers caractères du nom ou du village ou le n° CVI. Un menu déroulant apparent dans lequel vous pouvez sélectionner l'acheteur.";
        $json->help_popup_DR_caves = "Vous indiquez ici pour chacune des caves que vous aviez sélectionnées lors de l'étape précédente, le volume (hectolitres/litres qui vous a été communiqué par la Cave Coopérative. Ces volumes s'entendent après enrichissement. Si vous avez oublié de sélectionner une Cave coopérative, vous pouvez la rajouter ici en cliquant sur \" + acheteur\". Saisir les premiers caractères du nom ou du village ou le n° CVI. Un menu déroulant apparent dans lequel vous pouvez sélectionner l'acheteur.";
        $json->help_popup_DR_mouts = "Ventes de moûts destinés à l'élaboration de Crémant d'Alsace : à répartir par acheteur (hl/l). Les rebêches produites au titre des ventes de moûts restent chez le producteur. Vous devrez donc les inscrire en volume sur place (dans la rubrique rebêches). Sur votre DRM du mois de novembre, vous indiquerez 1) dans la colonne AOC Crémant d'Alsace, en entrée (ligne 10) le volume de Crémant d'Alsace que vous avez produit PLUS le volume de moûts que vous avez vendu. En ligne (c) sorties vrac : vous inscrirez le volume de moûts vendu. 2) Dans la colonne \"rebêches\" de la DRM vous inscrirez en entrée le volume total de rebêches que vous avez obtenu (comprenant les rebêches produites au titre des ventes de moûts). ";
        $json->help_popup_DR_vol_place = "Vous indiquez ici le volume que vous vinifiez sur place (chez vous)  (hectolitres/litres) Les volumes s'entendent hors lies déjà soutirées.";
        $json->help_popup_DR_vol_total_recolte = "Le volume total se calcule automatiquement en fonction de ce que vous avez saisi plus haut. Vous n'avez donc rien à saisir dans cette rubrique.";
        $json->help_popup_DR_vol_revendique = "Correspond au volume maximum que vous pouvez revendiqué dans le cépage ou l'appellation";
        $json->help_popup_DR_dplc = "Nous vous indiquons ici votre dplc en fonction de ce que vous avez saisi. Nous vous rappelons qu'en cas de dépassement d'un butoir cépage, c'est le DPLC de ce cépage qu'il faudra obligatoirement livrer en distillerie même si vous avez moins (ou pas du tout) de DPLC sur le total de l'appellation. Voir les exemples dans la notice explicative générale que vous pouvez consulter (et imprimer) dans l'écran \"mon espace CIVA\" ";
        $json->help_popup_DR_total_cepage = "Nous vous indiquons ici, en fonction de ce que vous avez saisi, la répartition de votre rendement dans le total cépage (volume revendiqué et DPLC éventuel). Si le rendement à l'ha apparaît en rouge c'est que vous dépassez le rendement maximum butoir de ce cépage ";
        $json->help_popup_DR_total_appellation = "Nous vous indiquons ici, la répartition de votre rendement sur le total de l'appellation (volume revendiqué et DPLC éventuel). Si le rendement à l'ha apparaît en rouge c'est que vous dépassez le rendement maximum autorisé de cette appellation";
        $json->help_popup_DR_recap_vente = "Il s'agit du récapitulatif des ventes en fonction de ce que vous avez saisi dans les écrans précédents. Vous complétez ici, la surface et le DPLC correspondant à chaque négociant ou cave coopérative.";

        $json->help_popup_autres = "Vous déclarez dans cet écran le volume global de lies de votre exploitation ainsi que la superficie des jeunes vignes ";
        $json->help_popup_autres_lies = "Le terme lies comprend à la fois les lies et les bourbes (définition communautaire du règlement 1493/99 du conseil du 17/5/99). Vous indiquez ici le volume global de lies (toutes appellations confondues) déjà soutirées qu'elles aient été livrées ou non. SI vous êtes vendeur de raisins ou adhérent à une cave coopérative vous êtes dispensé d'indiquer les lies correspondant aux raisins livrés. En revanche vous devez déclarer celles relatives au volume vinifié sur place (ce sont vos acheteurs et vos caves coopératives qui déclareront, globalement, les lies ultérieurement). Les lies que vous déclarez ici ne devront plus transiter par la DRM, même si vous ne les avez pas encore livrées (le document d'accompagnement fera foi). Pour les exploitations ayant déclarer du DPLC sur leur déclaration de récolte, les lies générées après la souscription de la déclaration de récolte pourront venir en déduction du DPLC. Ces volumes seront suivis sur la DRM.";
        $json->help_popup_autres_jv = "Indiquez ici la surface globale (ares,ca) de jeunes vignes (toutes appellations confondues). Les jeunes vignes correspondent aux 2 premières années de plantation, avant la 3e feuille).";
        $json->help_popup_validation = "Dans cet écran vous trouvez le récapitulatif de votre récolte par appellation en fonction de ce que vous avez saisi dans les étapes précédentes. Pour toute modification vous pouvez retournez à l'étape précédente, soit par le bouton \"retournez à l'étape précédente\" soit en cliquant sur l'option \"récolte\" en haut de l'écran.";
        $json->help_popup_validation_log_erreur = "Le logiciel vous informe ici des problèmes rencontrés sur votre déclaration. Ces erreurs sont bloquantes. Vous ne pourrez donc pas valider définitivement votre déclaration si elles ne sont pas règlées. En cliquant sur le message vous retournez automatiquement à l'endroit où se trouve le problème et vous pouvez effectuer la modification.";
        $json->help_popup_validation_log_vigilance = "le logiciel vous informe ici des problèmes rencontrés sur votre déclaration. Ces erreurs ne sont pas bloquantes et ne vous empêcheront pas de valider votre déclaration de récolte. En cliquant sur le message vous retournez automatiquement à l'endroit où se trouve le problème et vous pouvez effectuer la modification si besoin";

        /** INTRO **/
        $json->intro_mon_espace_civa_dr_validee = "Le logiciel vous informe ici des problèmes rencontrés sur votre déclaration. Ces erreurs sont bloquantes. Vous ne pourrez donc pas valider définitivement votre déclaration si elles ne sont pas règlées. En cliquant sur le message vous retournez automatiquement à l'endroit où se trouve le problème et vous pouvez effectuer la modification.";
        $json->intro_mon_espace_civa_dr_non_editable = "Votre DR n'est plus éditable.";
        $json->intro_gamma = "Vous aurez, ici, bientôt accès à l'application AlsaceGamm@.";
        $json->intro_doc_aide = "En cas de besoin n'hésitez pas à consulter la notice d'aide complete au format pdf.";
        $json->intro_exploitation_administratif = "Données administratives, n'hésitez pas à les modifier en cas de changement.";
        $json->intro_exploitation_acheteurs = "Veuillez cocher les cases correspondantes à la répartition de votre récolte.";
        $json->intro_exploitation_lieu = "Dans l'écran précédent vous avez cochez la (ou les) case AOC Alsace Grand cru. Vous devez maintenant ici sélectionner les lieux-dits pour lesquels vous avez récolté de l'AOC Alsace Grand Cru. Vous pouvez à tout moment supprimer la sélection à l'aide de la croix rouge.";
        $json->intro_exploitation_lieu_txt_gris = "Lieux-dits : ";
        $json->intro_declaration_recolte = "Saisissez ici votre récolte par cépage dans chaque appellation et lieu dit Grand Cru.";
        $json->intro_exploitation_autres = "Saisissez ici votre volume global de lies et vos jeunes vignes sans production.";
        $json->intro_validation = "Veuillez vérifier les informations saisies avant de valider votre déclaration. Vous pouvez à tout moment visualiser votre déclaration de récolte au format pdf en cliquant sur le bouton \"prévisualiser\" en bas de l'écran";
        


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
