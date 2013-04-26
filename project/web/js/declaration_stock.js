/**
 * Fichier : declaration_stock.js
 * Description : fonctions JS spécifiques à la déclaration de stock
 * Auteur : Mikaël Guillin - mguillin[at]actualys.com
 * Copyright: Actualys
 ******************************************/

/**
 * Initialisation
 ******************************************/
$(document).ready(function()
{	
	sommeColStock();
	navOngletsStock();
});

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


