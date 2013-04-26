/**
 * Fichier : declaration_stock.js
 * Description : fonctions JS spécifiques à la déclaration de stock
 * Auteur : Mikaël Guillin - mguillin[at]actualys.com
 * Copyright: Actualys
 ******************************************/

var appDS = $('#application_ds');
var champsSommes = appDS.find('.ligne_total input.somme');

/**
 * Initialisation
 ******************************************/
$(document).ready(function()
{	
	//navOngletsStock();
	initDSSommesCol();
});

/**
 * Calcul des sommes des colonnes de stocks
 *********************************************************/
var initDSSommesCol = function()
{
	// Parcours des champs
	champsSommes.each(function()
	{
		var champSomme = $(this);
		var col = $(champSomme.attr('data-somme-col'));
		var valDefaut = champSomme.attr('data-somme-col');
		var somme = calculSommeCol(col);
		
		// Si une valeur par défaut existe
		if(valDefaut) valDefaut = parseFloat(valDefaut);
		somme += valDefaut;
		
		champSomme.val(somme);
	});
	
};


var calculSommeCol = function(col)
{
	var champs = col.find('input[text]');
	var somme = 0;
	
	champs.each(function()
	{
		var champ = $(this);
		var val = champ.val();
		
		if(!val) val = 0;
		val = parseFloat(val);
		
		somme += val;
	});
	
	return somme;
};


/**
 * Calcul de la somme des champs des colonnes de stocks
 *********************************************************/
var sommeColStock = function()
{
	var champsColonne = $('#gestion_stock .colonne input');
	var blocSousTotal = $('#sous_total');
	var blocTotal = $('#total');

	champsColonne.blur(function()
	{
		// On récupère la colonne du champ
		var colonne = $(this).parents('.colonne');
		var champsColonne = colonne.find('input');
		var typeColonne = colonne.attr('id').substr(4);
		var champSousTotal = blocSousTotal.find('#soustotal_' + typeColonne);
		
		var champTotalTexte = blocTotal.find('.total_' + typeColonne).filter(':text');
		var champTotalCache = champTotalTexte.siblings(':hidden');
		
		var sommeSousTotal = 0;
		var sommeTotal = parseFloat(champTotalCache.val());
		
		// On parcourt les champs de la colonne
		champsColonne.each(function()
		{
			var champCourant = $(this);
			var valeurChampCourant = $.trim(champCourant.val());
			
			if(valeurChampCourant != '')
			{
				sommeSousTotal += parseFloat(valeurChampCourant);
				sommeTotal += parseFloat(valeurChampCourant);
			}
		});

		champSousTotal.val(sommeSousTotal);
		champTotalTexte.val(sommeTotal);
	});	
	
};

/**
 * Gère la navigation des onglets
 *********************************************************/
var navOngletsStock = function()
{
	var onglets = $('#onglets_majeurs.onglets_stock > li');
	var sousMenus = onglets.find('.sous_onglets');
	
	onglets.each(function()
	{
		var ongletCourant = $(this);
		var sousMenuCourant = ongletCourant.find('.sous_onglets');
		
		ongletCourant.hover(function()
		{
			onglets.removeClass('ui-tabs-selected');
			sousMenus.addClass('invisible');
			
			if(!ongletCourant.hasClass('ui-tabs-selected'))
			{
				ongletCourant.addClass('ui-tabs-selected');
				sousMenuCourant.removeClass('invisible');
			}
		});
	});
};


