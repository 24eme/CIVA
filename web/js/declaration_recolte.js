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
	$('#nouvelle_declaration').ready( function() { choixPrecDecla(); });
	$('#precedentes_declarations').ready( function() { accordeonPrecDecla(); });
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
		if(type_decla.filter(':checked').val() == 'type_declaration_1') liste_prec_decla.hide();
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
		var table_acht = $(this);
		
		var bloc = table_acht.parent();
		var form_ajout = bloc.next(".form_ajout");
		var btn_ajout = bloc.children('.btn');
		
		editerDonneesTable(table_acht);
		supprimerLigneTable(table_acht);
		
		initTableAjout(table_acht, form_ajout, btn_ajout);
		masquerTableAjout(table_acht, form_ajout, 0);
		
		btn_ajout.children('a.ajouter').click(function()
		{
			afficherTableAjout(table_acht, form_ajout, btn_ajout);
			return false;
		});
	});
};

/**
 * Edite les données d'un table
 ******************************************/
var editerDonneesTable = function(table_acht)
{
	var champs = table_acht.find('.editable');
	
	champs.editable(
	{
		submitBy: 'blur',
		onEdit: function(content)
		{
			if($(this).parent('td').hasClass('nom'))
			{
				var champ = $(this).find('input');
				champ.autocomplete(
				{
					source: sourceAutocompletion(table_acht)
				});
			}
		},
		onSubmit: function(content)
		{
			if($(this).parent('td').hasClass('nom'))
			{
				var champ = $(this).find('input');
				champ.autocomplete('destroy');
			}
		}
	});
};

/**
 * Supprime une ligne de la table courante
 ******************************************/
var supprimerLigneTable = function(table_acht)
{
	var btn = table_acht.find('tbody tr a.supprimer');
	
	btn.live('click', function()
	{
		$(this).parents('tr').remove();
		return false;
	});
};

/**
 * Initialise les fonctions des tables 
 * d'ajout
 ******************************************/
var initTableAjout = function(table_acht, form_ajout, btn_ajout)
{
	var table_ajout = form_ajout.find('table');
	var nom = form_ajout.find('td.nom input');
	var btn = form_ajout.find('.btn a');

//	autocompletionNomAcheteur(table_acht);

	btn.click(function()
	{
		if($(this).hasClass('valider'))
		{
			if(nom.val()=='')
			{
				alert("Veuillez renseigner le nom de l'acheteur");
				return false;
			}
			else alert("ajout ok");
		}
		
		masquerTableAjout(table_acht, form_ajout, 1);
		btn_ajout.show();
		
		return false;
	});
};

/**
 * Masque les tables d'ajout
 ******************************************/
var masquerTableAjout = function(table_acht, form_ajout, nb)
{
	var table = form_ajout.find('table');
	var nom = form_ajout.find('td.nom input');
	var champs_txt = table.find('input:text');
	var champs_cb = table.find('input:checkbox');
	
	champs_txt.attr("value",'');
	champs_cb.attr("checked",'');
	
	nom.autocomplete('destroy');
	
	form_ajout.hide();
	if(nb == 1) etatChampsTableAcht('');
};

/**
 * Afficher table ajout
 ******************************************/
var afficherTableAjout = function(table_acht, form_ajout, btn_ajout)
{
	var nom = form_ajout.find('td.nom input');
	
	etatChampsTableAcht('disabled');
	form_ajout.show();
	btn_ajout.hide();
	
	nom.autocomplete(
	{
		source: sourceAutocompletion(table_acht)
	});
};

/**
 * Active/Désactive tous les champs des
 * tables d'acheteurs
 ******************************************/
var etatChampsTableAcht = function(type)
{
	var tables_acheteurs = $('#exploitation_acheteurs table.tables_acheteurs');
	var champs = tables_acheteurs.find('input:checkbox');
	var champs_editables = tables_acheteurs.find('.editable');
	
	if(type == 'disabled')
	{
		champs.attr('disabled', 'disabled');
		champs_editables.editable('disable');
	}
	else
	{
		champs.attr('disabled', '');
		champs_editables.editable('enable');
	}
};

/**
 * Autocompletion
 ******************************************/
var sourceAutocompletion = function(table_acht)
{
	var noms = table_acht.find('td.nom span');
	var source = Array();
	
	noms.each(function(){ source.push($(this).text()); });
	source = $.unique(source);
	source.sort();
	
	return source;
};

