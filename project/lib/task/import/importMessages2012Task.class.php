<?php

class importMessages2012Task extends sfBaseTask
{

	 
	protected function configure()
	{

		$this->addOptions(array(
		new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
		new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
		new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
		// add your own options here
		new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'couchdb'),
		));

		$this->namespace = 'import';
		$this->name = 'Messages2012';
		$this->briefDescription = 'import messages 2012';
		$this->detailedDescription = <<<EOF
The [import|INFO] task does things.
Call it with:

  [php symfony import|INFO]
EOF;
	}

	protected function execute($arguments = array(), $options = array())
	{
		ini_set('memory_limit', '512M');
		set_time_limit('3600');
		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
		$connection = $databaseManager->getDatabase($options['connection'])->getConnection();

		$json = new stdClass();
		$json->_id = 'MESSAGES';
		$json->type = 'Messages';

		$json->msg_declaration_ecran_warning = "A définir";
		$json->msg_compte_index_intro  = "Pour créer votre compte, merci d'indiquer votre numéro CVI et votre code de création  (ceux que le CIVA vous a communiqués par courrier )";
		$json->msg_tiers_index_intro  = "Votre compte semble relié à plusieurs entités ayant des roles identiques. Afin d'éviter toute confusion, veuillez sélectionner celui que vous souhaitez utiliser lors de cette session.";
		$json->telecharger_pdf_mon_espace  = "Cette notice est au format PDF. Pour la visualiser, veuillez utiliser un <a href='http://pdfreaders.org/' >lecteur PDF</a>.";
		$json->telecharger_pdf  = "Le fichier généré est au format PDF. Pour le visualiser, veuillez utiliser un <a href='http://pdfreaders.org/' >lecteur PDF</a>.";
		$json->err_exploitation_acheteurs_popup_no_required  = "Veuillez cocher au moins une case pour continuer !";
		$json->err_exploitation_lieudits_popup_no_required  = "Veuillez séléctionner au moins un lieu-dit !";
		$json->err_dr_popup_no_lieu  = "Vous n'avez pas saisi de Lieu.";
		$json->err_dr_popup_no_superficie  = "Vous n'avez pas saisi de superficie. La surface est obligatoire sauf dans l'Edel";
		$json->err_dr_popup_min_quantite  = "Les rebêches doivent représenter au minimum 2 % du volume total produit";
		$json->err_dr_popup_max_quantite  = "Les rebêches doivent représenter au maximum 10% du volume total produit";
		$json->err_dr_popup_dest_rebeches  = "Vous n'avez pas respecté la répartition des rebêches.";
		$json->err_dr_popup_unique_lieu_denomination_vtsgn  = "Il faut distinguer la colonne par une mention complémentaire ou par une mention VT/SGN.";
		$json->err_dr_recap_vente_popup_superficie_trop_eleve  = "La somme des superficies des acheteurs ne peut pas être superieure au total de l'appellation";
		$json->err_dr_recap_vente_popup_dplc_trop_eleve  = "La somme des \"volumes en dépassement\" des acheteurs ne peut pas être supérieur au \"volume en dépassement\" attribuable aux acheteurs";
		$json->err_dr_recap_vente_popup_dplc_superieur_volume  = "Le 'volume en dépassement' ne peut pas être supérieur au 'volume acheté'";
		$json->err_log_lieu_non_saisie  = "Les détails de ce lieu n'ont pas été saisis";
		$json->err_log_cepage_non_saisie  = "Les détails de ce cépage n'ont pas été saisis";
		$json->err_log_detail_non_saisie  = "Le détail n'a pas été saisi";
		$json->err_log_ED_non_saisie  = "Vous nous avez indiqué comme motif de non récolte \"assemblage Edel\" mais vous n'avez pas saisi d'Edel";
		$json->err_log_usages_industriels_superieur_volume  = "Le volume d'usages industriels saisi ne peut pas être supérieur au volume total";
		$json->err_log_cremant_pas_rebeches  = "Vous avez oublié de saisir les rebêches";
		$json->err_log_cremant_min_quantite  = "Les rebêches doivent représenter au minimum 2% du volume total produit";
		$json->err_log_cremant_max_quantite  = "Les rebêches doivent représenter au maximum 10% des volumes produits";
		$json->err_log_cremant_rebeches_repartition = "Vous n'avez pas respecté la répartition des rebêches";
		$json->err_log_superficie_zero  = "Vous n'avez pas renseigné de detail pour cette appellation";
		$json->err_log_dplc  = "Vous dépassez le rendement butoir de ce cépage. Vous pouvez le replier en Edel. Si vous livrez votre raisin, ce repli ne peut se faire qu'en accord avec votre acheteur.";
		$json->err_log_recap_vente_non_saisie  = "Vous n'avez pas complété le récapitulatif des ventes";
		$json->err_log_recap_vente_non_saisie_superficie_dplc = "Vous n'avez pas complété toutes les superficies et/ou tous les volumes en dépassement dans le récapitulatif des ventes";
		$json->err_log_recap_vente_invalide  = "La surface et/ou le volume en dépassement du récapitulatif des ventes est supérieur au total de l'appellation";
		$json->err_log_recap_vente_dontdplc_trop_eleve = "Dans le récapitulatif des ventes, la somme des volumes en \"dont dépassement\" des acheteurs ne peut pas être supérieur au \"volume en dépassement\" attribuable aux acheteurs";
		$json->err_log_recap_vente_dontdplc_superieur_volume = "Dans le récapitulatif des ventes, le volume en \"dont dépassement\" d'un acheteur doit être inférieur à son volume vendu";
		$json->err_log_recap_vente_revendique_sur_place_negatif = "Le volume revendiqué sur place est négatif, la répartition du 'dont dépassement' n'est s'en doute pas correcte";
		$json->err_log_pas_calculer_revendique_sur_place = "Le volume revendiqué sur place ne peut pas être calculé car tous les volumes en dépassement de vos acheteurs n'ont pas été saisis dans le récapitulatif des ventes. Si vous n'affectez pas de dépassement à vos acheteurs, tapez O dans la zone \"dont dépassement\"";
		$json->help_popup_exploitation_administratif  = "Identification de l'exploitation : les renseignements affichés correspondent aux données que nous connaissons vous concernant. Vous pouvez procéder à des modifications, mais elles ne seront définitivement prises en compte, qu'après validation par le Service Douane Viticulture  de Colmar";
		$json->help_popup_exploitation_administratif_exploitation  = "Nom déclaré auprès de l'Administration des Douanes (nom de l'exploitant ou nom déclaré de la Société)";
		$json->help_popup_exploitation_administratif_gestionnaire  = "Nom de la personne désignée pour être l'interlocutrice de l'Administration";
		$json->help_popup_exploitation_administratif_siret  = "N° SIRET obligatoire pour les SA, SARL, SCA, GAEC, EARL…";
		$json->help_popup_mon_espace_civa  = "Vous êtes ici sur votre espace personnel totalement sécurisé. Vous pouvez y souscrire vos déclarations de récolte et de stocks en toute sécurité et en toute confidentialité et consulter celles des années précédentes.<br /> Et, si vous disposez d'un n° d'accises  vous pouvez  accéder à l'espace AlsaceGamm@ pour faire vos DAE et DSA. <br /> Vous avez à votre disposition une plateforme TEST pour vous entraîner.<br /> Si vous rencontrez des difficultés lors de l'établissement de vos DAE/DSA vous pouvez appeler la HOTLINE 03 80 24 41 95 (il s'agit d'une hotline entièrement prise en charge par le CIVA (il ne vous en coûtera que le prix de la communication) Il vous suffit de vous présenter comme opérateur alsacien et de donner votre n° d'accises.";
		$json->help_popup_mon_espace_civa_ma_dr  = "Vous pouvez saisir votre déclaration de récolte : <br /> 1) soit à partir d' une déclaration totalement vierge <br />     2) soit à partir de la déclaration d'une année précédente préremplie des surfaces (cette  option n'effacera en aucun cas les données de l'année sélectionnée qui seront toujours conservées) <br />     3) soit à partir des données préremplies par votre (vos) cave(s) coopérative(s) ou acheteurs de raisins. <br />  Une fois que vous avez commencez à saisir, vous  pouvez à tout moment supprimer la déclaration en cours, et recommencer.";
		$json->help_popup_mon_espace_civa_ma_ds  = "Vous pouvez saisir votre déclaration de stocks. <br />  1) Si vous détenez encore des stocks AOC, les produits qui s'afficheront sont ceux que vous avez déclaré \"sur place\" sur votre dernière déclaration de récolte. <br /> 2) Si vous ne détenez plus de stocks AOC, choisissez  l'option \"déclaration de stocks Néant\" puis \"démarrer\", et continuez jusqu'à la validation de votre déclaration.";
		$json->help_popup_mon_espace_civa_visualiser  = "Vous pouvez ici uniquement visualiser vos déclarations des années précédentes. Lorsque vous sélectionnerez une année, le logiciel générera un fichier au format pdf que vous pourrez consulter à l'écran ou imprimer.";
		$json->help_popup_mon_espace_civa_visualiser_ds  = "Vous pouvez visualiser vos déclarations de stocks des années précédentes.";
		$json->help_popup_mon_espace_civa_gamma  = "Vous pouvez ici accéder à l'espace \"AlsaceGamm@\", soit en temps réel (si vous avez déjà adhéré à Gamm@) soit  en environnement  TEST pour vous familiariser avec l'application\"<br/> Attention lorsque vous êtes dans l'environnement TEST, vous ne pouvez pas émettre des DAE en temps réel.\" Si vous rencontrez des difficultés lors de l'établissement de vos DAE/DSA vous pouvez appeler la HOTLINE 03 80 24 41 95 (il s'agit d'une hotline entièrement prise en charge par le CIVA (il ne vous en coûtera que le prix de la communication) Il vous suffit de vous présenter comme opérateur alsacien et de donner votre n° d'accises. Pour tout autre renseignement concernant cet espace vous pouvez appeler au CIVA 03 89 20 16 20 demander Dominique WOLFF ou Béatrice FISCHER";
		$json->help_popup_exploitation_acheteur  = "Vous renseignez ici la répartition de votre récolte par appellation. Vous identifiez également ici vos acheteurs de raisins et/ou les caves coopératives auprès desquelles vous êtes adhérent. <br />  Les renseignements de cet écran sont très importants. En effet, les écrans suivants seront générés en fonction de ce que vous cocherez ici. Cependant en cas d'oubli à ce stade, vous pourrez toujours rajouter un acheteur ou une appellation dans les étapes suivantes.  Utilisez les boutons d'aide  (?)  à côté de chaque rubrique pour vous faciliter la saisie&<br/&>. Attention les 'Alsace lieux-dits' et 'Alsace communales' revendiquées doivent être conformes aux cahiers des charges de l'AOC Alsace. Si vous ne souhaitez pas les revendiquer, vous déclarez les surfaces et les volumes correspondants dans l'AOC Alsace, mais dans ce cas vous ne précisez pas le nom du lieu-dit ou de la communale dans la rubrique 'dénomination complémentaire'.";
		$json->help_popup_exploitation_acheteur_vol_sur_place  = "Vous cochez ici les appellations qui concernent la partie de la récolte que vous avez gardée sur place (chez vous). Vous pouvez facilement cocher ou décocher les cases.\n";
		$json->help_popup_exploitation_acheteur_acheteurs_raisin  = "Vous sélectionnez ici vos acheteurs et vous renseignez les appellations pour chacun d'eux.  Pour sélectionner un acheteur de raisins, cliquez sur le bouton vert à droite \"AJOUTER UN ACHETEUR\". Puis à gauche dans la zone de saisie, vous tapez  les premières lettres du nom de l'acheteur, ou du village ou le n° CVI. Une liste déroulante apparaitra. Vous pourrez sélectionner l'acheteur concerné et cocher les appellations qui correspondent à votre livraison. Puis cliquez sur le bouton vert \"VALIDEZ\". <br />  La Cave BESTHEIM à Bennwihr a un statut de Négociant. Elle fait partie de la liste des \"acheteurs de raisins\" (elle ne figure pas dans la liste des \"Caves Coopératives\"). <br /> En cours de saisie, si vous vous êtes trompé vous pouvez à tout moment annuler (bouton rouge ANNULER) . Si votre acheteur n'est pas présent dans la liste vous pouvez nous appeler au 03 89 20 16 20 demander Dominique WOLFF ou Béatrice FISCHER.";
		$json->help_popup_exploitation_acheteur_caves_cooperatives  = "Vous sélectionnez ici la ou les caves coopératives auprès desquelles vous êtes adhérent et vous renseignez les appellations pour chacune d'elle. Pour sélectionner une Cave, cliquez sur le bouton vert à droite \"AJOUTER UNE CAVE\". Puis à gauche dans la zone de saisie, vous tapez  les premières lettres du nom de cave ou la commune ou le n° CVI. Une liste déroulante apparaitra. Vous pourrez sélectionner la Cave Coopérative concernée et cocher les appellations qui correspondent à votre livraison. Puis cliquez sur le bouton vert \"VALIDEZ\". <br />  La Cave BESTHEIM à Bennwihr a un statut de Négociant. Vous ne la trouverez pas dans la liste des Caves Coopératives. Elle fait partie de la liste des \"acheteurs de raisins\". <br /> En cours de saisie, si vous vous êtes trompé vous pouvez à tout moment annuler (bouton rouge ANNULER) . Une fois que vous avez validé la cave si vous voulez la supprimer, cliquez sur la croix rouge.";
		$json->help_popup_exploitation_acheteur_acheteurs_mouts  = "Si vous avez vendu des môuts destinés à l'élaboration de Crémant d' Alsace vous renseignez cette zone. Vous sélectionnez l'acheteur (négociant ou cave coopérative). Pour sélectionner un acheteur, cliquez sur le bouton vert à droite \"AJOUTER UN ACHETEUR\". Puis à gauche dans la zone de saisie, vous tapez  les premières lettres du nom ou la commune ou le n° CVI. Une liste déroulante apparaitra. Vous pourrez sélectionner le négociant ou la Cave Coopérative. Ici vous n'avez pas besoin de cocher l'appellation (l'appellation Crémant sera cochée par défaut).  Puis cliquez sur le bouton vert \"VALIDEZ\". En cours de saisie, si vous vous êtes trompé vous pouvez à tout moment annuler (bouton rouge ANNULER ) ou supprimer (croix rouge) .Attention si vous vendez des moûts de Crémant d'ALSACE, la rebêche restera chez vous. ";
		$json->help_popup_exploitation_acheteur_choix_mode_saisie_lie = "Vous choisissez ici la manière dont vous souhaitez déclarer les volumes d'usages industriels soutirés (lies connues). <br />Vous pouvez les inscrire soit par cépage, soit globalement par appellation. <br />Si vous les inscrivez par cépage, le logiciel vous calculera automatiquement votre volume revendiqué. Si la totalité de la récolte est logée sur place, vous aurez ainsi directement votre volume de vin clair (sous réserve que tous les soutirages aient été effectués). <br /><u>Attention</u> si vous choisissez de les inscrire par cépage, cela concernera toutes les appellations et la case \"dont usages industriels\" dans le récapitulatif de l'appellation ne sera plus accessible.";
		$json->help_popup_exploitation_lieu  = "Si dans l'écran précédent vous avez coché \"AOC Grands Crus\" ou \"AOC Alsace communale\" vous devez ici sélectionner les \"lieux-dits Grands Crus\" et/ou les \"communales\" concernés.";
		$json->help_popup_DR  = "Vous saisissez maintenant ici les surfaces et les volumes par cépage et par destination, dans les différentes appellations que vous avez sélectionnées dans l'écran précédent . En cas d'oubli vous pouvez à tout moment rajouter une appellation (cliquez sur + ajoutez une appellation) . Vous pouvez également rajouter un acheteur ou une cave coopérative. Utilisez pour cela les options (+ Acheteur) et (+ Cave).";
		$json->help_popup_DR_denomination  = "Dénomination complémentaire. Vous pouvez saisir ici une dénomination complémentaire autre  que les \"communales\" ou \"lieux-dits géographiques\". Indiquer également dans cette rubrique les \"replis\" et les \"vins sous charte\" par ex. Edel Gentil s'il fait l'objet d'un assemblage avant vinification.";
		$json->help_popup_DR_mention  = "A renseigner si vous avez récolté des Vendanges Tardives ou des Sélections de Grains Nobles . Sélectionner \"VT\" pour vendanges Tardives ou \"SGN\" pour Sélections de Grains Nobles. Attention toutes les parcelles en production doivent obligatoirement être récoltées à la date de souscription de la déclaration de récolte. Seules les Vendanges Tardives et Sélections de Grains Nobles ayant fait l'objet d'une déclaration préalable pourront déroger à cette obligation. SI c'est le cas vous devez indiquer la surface correspondante avec un volume estimatif (saisir \"estimation\" dans la rubrique \"dénomination complémentaire\". Une déclaration de récolte rectificative devra ensuite être effectuée auprès du Service Douanes Viticulture de Colmar qui transmettront automatiquement une copie au CIVA. Pour cela il vous faut imprimer votre déclaration sous format pdf auquel vous pouvez accéder en utilisant le bouton \"prévisualiser\".Il est important également que vous rectifiiez votre DRM en conséquence.";
		$json->help_popup_DR_superficie  = "La surface est obligatoire. A  saisir avec 2 décimales (ares,ca). Les surfaces déclarées doivent obligatoirement correspondre au relevé parcellaire enregistré dans votre casier viticole (fiche de compte du CVI).Cas particulier l'Edelzwicker :  vous laissez  la surface dans le cépage d'origine sauf pour les parcelles complantées en cépages en mélange. <br /> Si vous déclarez de l'edel \"repli butoir\" vous n'indiquez pas de surface (vous laisser les surfaces dans les cépages d'origine). En revanche pour l'Edel \"repli pinot noir\"  la surface est obligatoire. <br />  Il est possible de déclarer des surfaces sans volume. Mais lorsque vous validerez la colonne, le logiciel vous demandera de sélectionner un motif de \"non volume\" :  assemblage Edel, problèmes climatiques, maladie de la vigne, vendanges vertes, déclaration en cours, motifs personnels.";
		$json->help_popup_DR_vente_raisins  = "Vous indiquez ici pour chacun des acheteurs que vous aviez sélectionnés lors de l'étape précédente, le volume correspondant aux quantités de raisins livrés. A saisir avec 2 décimales (hectolitres/litres). Si votre acheteur vous communique les quantités en kilos, vous avez l'obligation de les convertir en hl/l  selon les coefficients forfaitaires suivants : AOC Alsace et Alsace Grand Cru (130 kgs = 1 Hl). AOC Crémant d'Alsace (150 kgs = 1 Hl). <br />  Si vous avez oublié de sélectionner un acheteur lors de l'étape précédente, vous pouvez le rajouter ici en cliquant sur  l'option \" + acheteur\". Saisir les premiers caractères du nom de l'acheteur, ou du village, ou son n° CVI d'acheteur si vous le connaissez. Un menu déroulant apparait dans lequel vous pouvez sélectionner l'acheteur.";
		$json->help_popup_DR_caves  = "Vous indiquez ici pour chacune des caves que vous aviez sélectionnées lors de l'étape précédente, le volume qui vous a été communiqué par la Cave Coopérative. A saisir avec 2 décimales (hectolitres/litres).  <br /> Si vous avez oublié de sélectionner une Cave coopérative lors de l'étape précédente, vous pouvez la rajouter ici en cliquant sur l'option \" + Cave\". Saisir les premiers caractères du nom de la Cave, ou du village, ou son n° CVI si vous le connaissez. Un menu déroulant apparait dans lequel vous pouvez sélectionner la Cave Coopérative.\n";
		$json->help_popup_DR_mouts  = "Ventes de moûts destinés à l'élaboration de Crémant d'Alsace : à répartir par acheteur (hl/l). <br /> Les rebêches produites au titre des ventes de moûts restent chez le producteur. Vous devrez donc les inscrire en volume sur place (dans la rubrique rebêches). <br /> Sur votre DRM du mois de novembre, vous indiquerez <br /> 1) dans la colonne AOC Crémant d'Alsace, en entrée (ligne 10) le volume de Crémant d'Alsace que vous avez produit PLUS le volume de moûts que vous avez vendu. En ligne (c) sorties vrac : vous inscrirez le volume de moûts vendu. <br /> 2) Dans la colonne \"rebêches\" de la DRM vous inscrirez en entrée le volume total de rebêches que vous avez obtenu (comprenant les rebêches produites au titre des ventes de moûts).";
		$json->help_popup_DR_vol_place  = "vous indiquez ici le volume que vous vinifiez sur place (chez vous). A saisir avec 2 décimales (Hectolitres/litres). <br />  Le volume à inscrire est le volume total récolté même si vous avez déjà effectué des soutirages \n";
		$json->help_popup_DR_vol_total_recolte  = "Le volume total se calcule automatiquement en fonction de ce que vous avez saisi plus haut. Vous n'avez donc rien à saisir dans cette rubrique.";
		$json->help_popup_DR_vol_revendique  = "Correspond au volume maximum que vous pouvez revendiqué dans le cépage ou l'appellation";
		$json->help_popup_DR_usages_industriels  = "Cette ligne est accessible uniquement si vous avez coché au départ que vous souhaitez insrire vos usages industriels (lies connues) par cépage.";
		$json->help_popup_DR_dplc  = "Nous vous indiquons ici votre volume en dépassement en fonction de ce que vous avez saisi. <br />  Nous vous rappelons que dans l'AOC Alsace, en cas de dépassement d'un butoir cépage, c'est le dépassement de ce cépage qu'il faudra obligatoirement livrer en distillerie même si vous avez moins (ou pas du tout) de dépassements sur le total de l'appellation. Voir les exemples dans la notice explicative complète  que vous pouvez télécharger .\n";
		$json->help_popup_DR_total_cepage  = "Nous vous indiquons ici, en fonction de ce que vous avez saisi, la répartition de votre rendement dans le total cépage (volume revendiqué et dépassement éventuel). <br /> Dans l'AOC Alsace, si le rendement à l'ha apparaît en rouge c'est que vous dépassez le rendement maximum butoir de ce cépage.<br />  Dans l'AOC Grand Cru le rendement se calcule  par cépage dans chacun des lieux-dits. S'il apparaît en rouge c'est que vous dépassez le maximum autorisé de ce cépage dans le lieu-dit Grand Cru concerné.\n";
		$json->help_popup_DR_total_couleur  = "total couleur";
		$json->help_popup_DR_total_couleur_alternatif  = "Dans cette appellation le rendement s'entend par couleur (blanc ou rouge). Cette colonne est inactive parce que vous êtes en train de saisir une autre couleur";
		$json->help_popup_DR_total_appellation  = "Nous vous indiquons ici sur le total appellation, le rendement à l'ha, le volume revendiqué et le dépassement éventuel. <br />Si vous êtes en dépassement, le rendement apparaît en rouge. <br />  ATTENTION : dans l'AOC Alsace, les zones \"volume revendiqué\" et \"volume en dépassement \" sont doublées. Les 2 premières correspondent au calcul du rendement de l'appellation. Les 2 suivantes correspondent à la somme des volumes revendiqués et des dépassements de butoirs cépages. Voir les exemples dans la notice explicative complète que vous pouvez télécharger. <br />  Dans l'AOC Alsace \"communale\" le rendement se calcule par couleur,  pour chaque communale distinctement . <br /> Dans l'AOC Alsace \"lieux-dits\" le rendement se calcule par couleur, tous lieux-dits confondus\n.";
		$json->help_popup_DR_recap_vente  = "Il s'agit du récapitulatif des ventes en fonction de ce que vous avez saisi dans les écrans précédents. Vous complétez ici, la surface correspondante à chaque négociant et/ou cave coopérative ainsi que, le cas échéant, le volume en dépassement qui revient à chacun. Si vous décidez de livrer vous même le dépassement il faut taper 0 dans la zone \"dont dépassement\, sinon  le logiciel ne pourra pas vous calculer le volume revendiqué sur place";
		$json->help_popup_recapitulatif_ventes  = "Cet écran affiche le récapitulatif de l’appellation. <br /> L’onglet orange, dans le haut de l’écran, vous indique dans quelle appellation vous vous trouvez. <br /> Si vous constatez une erreur, vous pouvez retourner à la saisie des cépages de l’appellation et effectuer les corrections. <br /> <br /> Cet écran reprend également  la répartition des ventes et des apports caves (si vous en avez saisis. <br /> Vous renseignez  dans la rubrique \"récapitulatif des ventes\" la surface et le dépassement éventuel correspondants à chaque vente ou apport.\n";
		$json->help_popup_DR_recap_usages_industriels = "Vous retrouvez dans cette rubrique le récapitulatif des usages industriels. <br /> 
			<u>Usages industriels saisis</u> = la somme des lies saisies dans les cépages si vous aviez opté pour cette solution. En revanche si vous aviez choisi de les inscrire globalement par appellation, vous les inscrivez maintenant dans cette ligne.<br />
			<u>Dépassement</u> = volume éventuel de dépassement de rendement en fonction de votre saisie.<br />
			Si votre dépassement de rendement est inférieur à votre volume de lies, c'est le volume de lies qui apparait dans la ligne \"usages industriels globaux\". Au contraire si le volume de lies saisies est inférieur au dépassement, c'est ce dernier qui apparait dans la ligne \"usages industriels globaux\".";
		$json->help_popup_DR_recap_usages_industriels_saisies = "Cette rubrique est accessible uniquement si vous avez coché au départ que vous souhaitez inscrire vos usages industriels (lies connues) globalement par appellation. Si vous aviez coché l'option \"par cépage\", vous retrouverez ici la somme des lies saisies dans les cépages. En aucun cas vous ne pourrez la modifier ici.";
		$json->help_popup_autres  = "Vous  indiquez  ici la superficie globale de vos jeunes vignes\n";
		$json->help_popup_autres_jv  = "Indiquez ici la surface globale (ares,ca) de jeunes vignes (toutes appellations confondues). Les jeunes vignes correspondent aux 2 premières années de plantation, avant la 3e feuille).";
		$json->help_popup_validation  = "Dans cet écran vous trouvez le récapitulatif de votre récolte par appellation en fonction de ce que vous avez saisi dans les étapes précédentes. Pour toute modification vous pouvez retournez à l'étape précédente, soit par le bouton  \"retournez à l'étape précédente\" soit en cliquant sur l'étape 2 \"récolte\" en haut de l'écran";
		$json->help_popup_validation_log_erreur  = "Le logiciel vous informe ici des problèmes rencontrés sur votre déclaration. Ces erreurs sont bloquantes. Vous ne pourrez donc pas valider définitivement votre déclaration si elles ne sont pas règlées.<br />  En cliquant sur le message vous retournez automatiquement à l'endroit où se trouve le problème et vous pouvez effectuer la modification";
		$json->help_popup_validation_log_vigilance  = "Le logiciel vous informe ici des problèmes rencontrés sur votre déclaration. Ces erreurs ne sont pas bloquantes et ne vous empêcheront pas de valider votre déclaration de récolte. <br /> En cliquant sur le message vous retournez automatiquement à l'endroit où se trouve le problème et vous pouvez effectuer la modification si besoin";
                $json->help_popup_validation_log_vigilance_ds  = "Le logiciel vous informe ici des problèmes rencontrés sur votre déclaration. Ces erreurs ne sont pas bloquantes et ne vous empêcheront pas de valider votre déclaration de Stock. <br /> En cliquant sur le message vous retournez automatiquement à l'endroit où se trouve le problème et vous pouvez effectuer la modification si besoin";
		$json->intro_mon_espace_civa_dr  = "Vous pouvez interrompre la saisie de votre déclaration à tout moment, vos données seront conservées.";
                $json->intro_mon_espace_civa_dr_validee  = "Vous avez déjà validé votre déclaration. Vous ne pouvez plus la modifier. Vous pouvez uniquement la visualiser et l'imprimer. En cas de problème contactez au CIVA  Dominique WOLFF ou Béatrice FISCHER";
		$json->intro_mon_espace_civa_dr_non_disponible  = "Le service est momentanément indisponible. Essayez de vous reconnecter ultérieurement.";
		$json->intro_mon_espace_civa_dr_non_editable  = "La date limite pour la souscription des déclarations de récolte est dépassée (10 décembre). Pour toute question, veuillez contacter directement le CIVA.";
		$json->intro_mon_espace_civa_dr_non_ouverte  = "Le Téléservice pour la déclaration de récolte 2013 sera ouvert à partir du 28 octobre";
		$json->intro_mon_espace_civa_ds_validee  = "Votre Décaration de Stocks est validée. Vous ne pouvez plus la modifier. Vous pouvez uniquement la visualiser et l'imprimer. En cas de problème contactez au CIVA  Dominique WOLFF ou Béatrice FISCHER";
		$json->intro_mon_espace_civa_ds_non_editable  = "La date limite pour la souscription des Déclarations de stocks est dépassée (31 août). Pour toute question, veuillez contacter directement le CIVA.";
		$json->intro_mon_espace_civa_ds_non_ouverte  = "Le Téléservice pour la Déclaration de Stocks 2013 sera ouvert à partir du 25 juillet.";
		$json->intro_mon_espace_civa_ds  = "Vous pouvez interrompre la saisie de votre déclaration à tout moment, vos données seront conservées.";
		$json->help_popup_mon_espace_ds_general = "Vous saisissez ici votre déclaration de stocks de fin de campagne au 31 juillet. <br /> Cadre règlementaire :  les personnes physiques ou morales (ou groupements de ces personnes) autres que les consommateurs privés et les détaillants, doivent présenter chaque année une déclaration de stocks de moûts de raisins, de moûts de raisins concentrés, de moûts de raisins concentrés rectifiés et de vins qu'ils détiennent à la date du 31 juillet (art.11 du RG/CE 436/2009 de la Commission portant modalités d'application du RG /CE 479/2008 du Conseil). <br /> Les opérateurs dont le stock de vins est à néant doivent déposer une déclaration de stocks \"néant\". <br /> En outre l'art. 408 du CGI (modifié par la loi n° 2008-1443 du 30/12/2008) précise que la déclaration des stocks restant dans les caves des producteurs doit être souscrite chaque année, avant le 1er septembre, dans les mêmes conditions que celles prévues à l'art.407. Ce dernier précise que la déclaration doit être souscrite par voie électronique auprès de l'Administration des Douanes. En Alsace il est convenu que la déclaration par voie électronique doit être souscrite exclusivement sur le portail du CIVA \" Vinsalsace.pro\".<br />  Le défaut de déclaration dans le délai règlementaire entraîne l'exclusion du bénéfice des mesures d'aides communautaires.";
                $json->intro_mon_espace_civa_no_lieux_de_stockage  = "Aucune Déclaration de Stocks ne peut être saisie car vous ne possèdez pas de lieu de stockage. Veuillez vous adresser au CIVA auprès de Dominique WOLFF ou Béatrice FISCHER afin d'enregistrer votre ou vos lieux de stockage.";
                $json->help_popup_ds_lieux_stockage = "Il s'agit des lieux de stockage déclarés pour votre exploitation auprès des Services des Douanes. <br />  Nous avons précoché (de la même manière pour chacun des lieux de stockage), les appellations pour lesquelles vous avez déclaré du volume \"sur place\" sur votre dernière déclaration de récolte.  <br /> Vous pouvez aisément cocher/décocher les appellations en fonction de vos besoins par lieu de stockage.<br />  Cet écran est important : en effet les écrans suivants seront générés en fonction de ce que vous cocherez ici.";
                $json->help_popup_ds_lieux_stockage_neant = "Si vous détenez encore du stock de vins sans IG (vins de table), décochez la case \"DS Néant\" et cochez la case \"vins sans IG\" dans le lieu de stockage correspondant.";
                $json->help_popup_ds_autres = "Les volumes correspondant aux vins en dépassements de rendements et aux rebêches ne doivent pas être inclus dans les volumes que vous avez déclaré dans les cépages/appellations. Ils sont à distinguer ici. <br />  Les lies doivent en principe être livrées avant le 31 juillet de la campagne. Toutefois les opérateurs qui détiennent encore un volume de lies non livré au 31 juillet doivent le déclarer ici.";
                $json->help_popup_ds_validation = "Nous vous affichons ici le récapitulatif de votre déclaration de stocks. Ces éléments vous sont utiles pour remplir votre DRM.  Les rubriques correspondent aux rubriques de la DRM.";
                $json->help_popup_validation_vins_sans_ig = "Si vous voulez saisir des Vins sans IG, il faut retourner à l'étape \"lieux de stockage\" et cocher la case \"Vins sans IG\".";
                $json->intro_gamma  = "Vous avez accès ici à l'application AlsaceGamm@.";
		$json->intro_doc_aide  = "En cas de besoin n'hésitez pas à consulter la notice d'aide complete au format pdf.";
		$json->intro_exploitation_administratif  = "Données administratives, n'hésitez pas à les modifier en cas de changement.";
		$json->intro_exploitation_acheteurs  = "Veuillez cocher les cases correspondantes à la répartition de votre récolte.";
		$json->intro_exploitation_lieu_grdcru  = "Dans l'écran précédent vous avez cochez  AOC Alsace Grands Crus. Vous devez maintenant ici sélectionner les lieux-dits pour lesquels vous avez récolté de l'AOC Alsace Grand Cru. Vous pouvez à tout moment supprimer la sélection à l'aide de la croix rouge.";
		$json->intro_exploitation_lieu_communale  = "Dans l'écran précédent vous avez coché \"Alsace communale\". Vous devez maintenant ici sélectionner les appellations communales concernées. Vous pouvez à tout moment supprimer la sélection à l'aide de la croix rouge.";
		$json->intro_exploitation_lieu_txt_gris_grdcru  = "Lieux-dits Grands Crus :";
		$json->intro_exploitation_lieu_txt_gris_communale  = "Communales :";
		$json->intro_declaration_recolte  = "Saisissez ici votre récolte par cépage dans chacune des appellations que vous avez sélectionnée au début de votre déclaration";
		$json->intro_exploitation_autres  = "Saisissez ici vos jeunes vignes sans production.\n";
		$json->intro_validation  = "Veuillez vérifier les informations saisies avant de valider votre déclaration. Vous pouvez à tout moment visualiser votre déclaration de récolte au format pdf en cliquant sur le bouton \"prévisualiser\" en bas de l'écran";
		$json->msg_declaration_ecran_warning_precedente  = "<u>Attention :</u> afin de respecter les dispositions prévues dans le cahier des charges de l'AOC Alsace en matière de revendication des  nouvelles appellations  \"communales\" et \"lieux-dits géographiques\" :<ul><li>le Klevener de Heiligenstein doit être déclaré maintenant dans l'onglet \"Alsace Communale\". Si vous démarrez votre déclaration 2013 à partir de la déclaration d'une année précédente dans laquelle vous aviez déclaré du Klevener de Heiligenstein, les données correspondantes au KdeH ne seront pas récupérées (lié à des difficultés techniques)</li><li>les \"lieux-dits géographiques\" revendiqués seraient à saisir dans l'onglet \"Alsace lieu-dit\"</li></ul></li>\n";
		$json->msg_declaration_ecran_warning_pre_import  = "Ci-dessous la liste de vos caves ou acheteurs qui ont pré-rempli à ce jour les données vous concernant :";
		$json->msg_declaration_ecran_warning_post_import  = "Vous pourrez aisément compléter, rectifier et valider les données pré-remplies.";
		$json->intro_exploitation_lieu_txt_consigne_communale  = "Sélectionnez une commune dans la liste suivante :";
                $json->intro_exploitation_lieu_txt_consigne_grdcru  = "Sélectionnez un lieu-dit Grand Cru dans la liste suivante :";
		$json->intro_exploitation_lieu_txt_label_grdcru  = "Ajoutez un lieu-dit Grand Cru :";
		$json->intro_exploitation_lieu_txt_label_communale = "Ajoutez une Communale :";
                
        $json->help_popup_DR_lieu_dit = "Vous avez coché une case \"AOC Alsace lieu-dit\" dans l'écran \"répartition de la récolte\". Vous devez renseigner ici le nom du ou des lieux-dits géographiques concernés";
		$json->dr_notice_evolutions = "<h2>Usages Industriels </h2>"
                        . "<p>Vous pouvez désormais choisir de saisir <u>par cépage</u> les volumes d’usages industriels soutirés (lies connues). Le système vous calculera automatiquement votre volume revendiqué. Si la totalité de la récolte est logée sur place, vous aurez ainsi directement votre volume de vin clair (sous réserve que tous les soutirages aient été effectués).</p>"
                        . "<p>Attention : si vous sélectionnez l’option «cépage»,  cela concernera toutes les appellations et la case « dont usages industriels » dans le <u>récapitulatif de l’appellation</u> ne sera plus accessible.</p>"
                        . "<p>Cependant si vous le souhaitez, il est toujours possible d’indiquer les usages industriels <u>globalement par appellation</u>.</p>"
                        . "<p>Rendez vous dans l’onglet «&nbsp;répartition de la récolte&nbsp;», et pages 7,8,9  de la notice explicative que vous pouvez télécharger.&nbsp;&nbsp;&nbsp;<a href='/telecharger_la_notice' style='display:inline-block; height: 20px; float: none;' class='telecharger-btn'></a></p>"
                        . "<br/><h2>Autres nouveautés</h2>"
                        . "<p>Pour vous faciliter l’inscription de votre récolte sur la DRM, vous trouverez en fin de saisie, une annexe spécifique reprenant les données de votre récolte sur place, conformément aux colonnes de la DRM.</p>"
                        . "<p>Vous pouvez désormais d’un simple clic, envoyer à votre (ou vos) acheteur ou cave coopérative, les données relatives à votre déclaration de récolte</p><br/>";

		$json->help_popup_DR_recap_appellation_usage_industriel  = "A définir";
		$json->err_log_lieu_usages_industriels_inferieur_dplc = "Le volume d'usages industriels est inférieur au volume minimum requis";

                $json->stock_null_appellation  = "Les stocks de cette appellation n'ont pas été saisis";
		$json->stock_zero_appellation  = "Les stocks de cette appellation sont saisis à nul";
		$json->stock_null_lieu  = "Les stocks de ce lieu n'ont pas été saisis";
		$json->stock_zero_lieu  = "Les stocks de ce lieu sont saisis à nul";    
		$json->stock_null_cepage  = "Les stocks de ce cépage n'ont pas été saisis";
		$json->stock_zero_cepage  = "Les stocks de ce cépage sont saisis à nul";
		$json->stock_aucun_produit  = "La DS n'a aucun produit et n'a pas été signalée comme une DS Néant";    
		
                
		$docs[] = $json;
		if ($options['import'] == 'couchdb') {
			foreach ($docs as $data) {
				$doc = acCouchdbManager::getClient()->find($data->_id);
				if ($doc) {
					$doc->delete();
				}
				$doc = acCouchdbManager::getClient()->createDocumentFromData($data);
				$doc->save();
			}
			return;
		}
		echo '{"docs":';
		echo json_encode($docs);
		echo '}';
	}
}

