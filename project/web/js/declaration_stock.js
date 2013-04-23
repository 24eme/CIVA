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
	//navOngletsStock();
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
		var sommeColonne = 0;
		
		// On parcourt les champs de la colonne
		champsColonne.each(function()
		{
			var champCourant = $(this);
			var valeurChampCourant = $.trim(champCourant.val());
			
			if(valeurChampCourant != '')
			{
				sommeColonne += parseFloat(valeurChampCourant);
			}
		});

		blocSousTotal.find('#soustotal_' + typeColonne).val(sommeColonne);
	});
};

/**
 * Gère la navigation des onglets
 *********************************************************/
var navOngletsStock = function()
{
	var onglets = $('#onglets_majeurs li');
	
	onglets.each(function()
	{
		var ongletCourant = $(this);
		var sousMenu = ongletCourant.find('.sous_onglets');
		
		ongletCourant.hover(function()
		{
			onglets.removeClass('ui-tabs-selected');
			
			if(!ongletCourant.hasClass('ui-tabs-selected'))
			{
				ongletCourant.addClass('ui-tabs-selected');
			}
		});
	});
};


