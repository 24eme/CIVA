/**
 * Fichier : declaration_recolte.js
 * Description : fonctions JS spécifiques à la déclaration de récolte
 * Auteur : Hamza Iqbal - hiqbal[at]actualys.com
 * Copyright: Actualys
 ******************************************/

/**
 * Initialisation
 ******************************************/
$(document).ready( function()
{
	
	hauteurEgale($('#onglets_majeurs li a'));
	if($("#principal").hasClass('ui-tabs')) $("#principal").tabs();
	$('#precedentes_declarations').ready( function() { accordeonPrecDecla(); });
	$('#nouvelle_declaration').ready( function() { choixPrecDecla(); });
	$('#gestionnaire_exploitation').ready( function() { formGestionnaireExploitation(); });
	$('.table_donnees').ready( function() { initTablesDonnes(); });
	$('#exploitation_acheteurs').ready( function() { initTablesAcheteurs(); });
});

/**
 * Choix d'un précédente déclaration
 ******************************************/
var choixPrecDecla = function()
{
	var nouvelle_decla = $('#nouvelle_declaration');
	var liste_prec_decla = nouvelle_decla.find('select');
	var type_decla = nouvelle_decla.find('input[name="dr[type_declaration]"]');
	
	liste_prec_decla.hide();
	
	type_decla.change(function()
	{
		if(type_decla.filter(':checked').val() == 'vierge') liste_prec_decla.hide();
		else liste_prec_decla.show();
	});
};

/**
 * Accordéon précédentes déclarations
 ******************************************/
var accordeonPrecDecla = function()
{
	$('#precedentes_declarations ul.ui-accordion').accordion(
	{
		autoHeight: false,
		active: 0
	});
};

/**
 * Formulaire de modification du
 * gestionnaire de l'exploitation
 ******************************************/
var formGestionnaireExploitation = function()
{
	var bloc = $('#gestionnaire_exploitation');
	var presentation_infos = bloc.find('#presentation_infos');
	var modification_infos = bloc.find('#modification_infos');
	var btn_modifier = presentation_infos.find('a.modifier');
	var btn_annuler = modification_infos.find('a.annuler');
	var datepicker = modification_infos.find('input.datepicker');
	var annee = new Date().getFullYear();
	
	//	modification_infos.hide();
	
	btn_modifier.click(function()
	{
		presentation_infos.hide();
		modification_infos.show();
		return false;
	});
	
	btn_annuler.click(function()
	{	
		presentation_infos.show();
		modification_infos.hide();
		return false;
	});
	
	$('.datepicker').datepicker(
	{
		changeMonth: true,
		changeYear: true,
		dateFormat: 'dd/mm/yy',
		dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
		dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
		firstDay: 1,
		monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
		monthNamesShort: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
		yearRange: '1900:'+annee
	});
};

/**
 * Initialise les fonctions des tables 
 * de données
 ******************************************/
var initTablesDonnes = function()
{
	var tables = $('table.table_donnees');
	
	tables.each(function()
	{
		var table = $(this);
		styleTables(table);
	});
};


/**
 * Ajoute les classes nécessaires pour la
 * mise en forme des tables
 ******************************************/
var styleTables = function(table)
{
	var tr = table.find('tbody tr');
	
	tr.each(function()
	{
		$(this).find('td:odd').addClass('alt');
	});
};

/**
 * Initialise les fonctions des tables 
 * d'acheteurs
 ******************************************/
var initTablesAcheteurs = function()
{
	var tables_acheteurs = $('#exploitation_acheteurs table.tables_acheteurs');
	
	tables_acheteurs.each(function()
	{
		var table_achet = $(this);
		
		var bloc = table_achet.parent();
		var form_ajout = bloc.next(".form_ajout");
		var btn_ajout = bloc.children('.btn');
		
		if(bloc.attr('id') != 'cave_particuliere')
		{
			toggleTrVide(table_achet);
			supprimerLigneTable(table_achet);
			
			initTableAjout(table_achet, form_ajout, btn_ajout);
			masquerTableAjout(table_achet, form_ajout, 0);
			
			btn_ajout.children('a.ajouter').click(function()
			{
				afficherTableAjout(table_achet, form_ajout, btn_ajout);
				return false;
			});
		}
	});
};

/**
 * Affiche/masque la première ligne
 * d'un tableau
 ******************************************/
var toggleTrVide = function(table_achet)
{	
	var tr = table_achet.find('tbody tr');
	var tr_vide = tr.filter('.vide');
	tr_vide.next('tr').addClass('premier');

	if(tr.size()>1) tr_vide.hide();
	else tr_vide.show();
};

/**
 * Supprime une ligne de la table courante
 ******************************************/
var supprimerLigneTable = function(table_achet)
{
	var btn = table_achet.find('tbody tr a.supprimer');
	
	btn.live('click', function()
	{
		var choix = confirm('Confirmez-vous la suppression de cette ligne ?');
		if(choix)
		{
			$(this).parents('tr').remove();
			toggleTrVide(table_achet);
		}
		return false;
	});
};


var filtrer_source = function(i)
{
	return i['value'].split('|@');
};

/**
 * Initialise les fonctions des tables 
 * d'ajout
 ******************************************/
var initTableAjout = function(table_achet, form_ajout, btn_ajout)
{
	var table_ajout = form_ajout.find('table');
	var source_autocompletion = eval(table_ajout.attr('rel'));
	var champs = table_ajout.find('input');
	var nom = table_ajout.find('td.nom input');
	var cvi = table_ajout.find('td.cvi');
	var commune = table_ajout.find('td.commune');
	var btn = form_ajout.find('.btn a');
	var acheteur_mouts = 0;
	
	nom.autocomplete(
	{
		minLength: 0,
		source: source_autocompletion,
		focus: function(event, ui)
		{
			nom.val(ui.item[0]);
			cvi.find('span').text(ui.item[1]);
			cvi.find('input').val(ui.item[1]);
			commune.find('span').text(ui.item[2]);
			commune.find('input').val(ui.item[2]);
			
			return false;
		},
		select: function(event, ui)
		{	
			nom.val(ui.item[0]);
			cvi.find('span').text(ui.item[1]);
			cvi.find('input').val(ui.item[1]);
			commune.find('span').text(ui.item[2]);
			commune.find('input').val(ui.item[2]);
				
			return false;
		}
	}); 
	
	
	nom.data('autocomplete')._renderItem = function(ul, item)
	{
		var tab = item['value'].split('|@');
		
		return $('<li></li>')
		.data("item.autocomplete", tab)
		.append('<a><span class="nom">'+tab[0]+'</span><span class="cvi">'+tab[1]+'</span><span class="commune">'+tab[2]+'</span></a>' )
		.appendTo(ul);
	};
	
	btn.click(function()
	{
		if(table_achet.parent().attr('id') == 'acheteurs_mouts') acheteur_mouts = 1;
		
		if($(this).hasClass('valider'))
		{
			if(nom.val()=='')
			{
				alert("Veuillez renseigner le nom de l'acheteur");
				return false;
			}
			else
			{
				var donnees = Array();
				
				champs.each(function()
				{
					var chp = $(this)
					if(chp.attr('type') == 'text' || chp.attr('type') == 'hidden') donnees.push(chp.val());
					else
					{
						if(chp.is(':checked')) donnees.push("1");
						else donnees.push("0");
					}
				});
				
				$.post("../ajax.php",
				{ action: "ajout_ligne_table", donnees: donnees, acheteur_mouts: acheteur_mouts },
				function(data)
				{
					var tr = $(data);
					tr.appendTo(table_achet);
					toggleTrVide(table_achet);
					styleTables(table_achet);
				});
			}
		}
		
		masquerTableAjout(table_achet, form_ajout, 1);
		btn_ajout.show();
		
		return false;
	});
};

/**
 * Masque les tables d'ajout
 ******************************************/
var masquerTableAjout = function(table_achet, form_ajout, nb)
{
	var table = form_ajout.find('table');
	var spans = form_ajout.find('tbody td span');
	var champs_txt = table.find('input:text,input[type=hidden]');
	var champs_cb = table.find('input:checkbox');
	
	spans.text('');
	champs_txt.attr("value",'');
	champs_cb.attr("checked",'');
	
	form_ajout.hide();
	if(nb == 1) etatChampsTableAcht('');
};

/**
 * Afficher table ajout
 ******************************************/
var afficherTableAjout = function(table_achet, form_ajout, btn_ajout)
{
	form_ajout.show();
	btn_ajout.hide();
	etatChampsTableAcht('disabled')
};

/**
 * Active/Désactive tous les champs des
 * tables d'acheteurs
 ******************************************/
var etatChampsTableAcht = function(type)
{
	var tables_acheteurs = $('#exploitation_acheteurs table.tables_acheteurs');
	var champs = tables_acheteurs.find('input:checkbox');
	var btns_supprimer = tables_acheteurs.find('a.supprimer');
	var btns = tables_acheteurs.next('.btn');
	
	if(type == 'disabled')
	{
		champs.attr('disabled', 'disabled');
		btns_supprimer.hide();
		btns.hide();
	}
	else
	{
		champs.attr('disabled', '');
		btns_supprimer.show();
		btns.show();
	}
};
