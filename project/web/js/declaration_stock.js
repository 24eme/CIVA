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
    initLieuxStockage();
    var ajaxForm = $('form.ajaxForm');
    if(ajaxForm.length > 0) {
        ajaxForm.ajaxPostForm();
    }
    initValidDSPopup();
});


var initLieuxStockage = function()
{
    var checkboxes = $(".table_donnees input");
    checkboxes.each(function(){
        $(this).click(function(){
            majLieuNeant();
            });
    });
    $("#ds_lieu_neant").change(function(){
        majCheckboxesAppellation();
    });
}

var majCheckboxesAppellation = function(){
    if($("#ds_lieu_neant").is(":checked")){
        $(".table_donnees input").each(function(){
            $(this).attr("disabled","disabled");
        });
    }else{
       $(".table_donnees input").each(function(){
            $(this).removeAttr("disabled");
        }); 
    }
}

var majLieuNeant = function(){
    var one_checked = false;
    $(".table_donnees input").each(function(){
        if($(this).is(":checked")){
            one_checked = true;
        }
    });
    if(one_checked)
        $("#ds_lieu_neant").attr("disabled","disabled");
    else
        $("#ds_lieu_neant").removeAttr("disabled");
            
}


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
		var valDefaut = champSomme.attr('data-val-defaut');
		var somme = col.calculSommeCol();
		
		// Si une valeur par défaut existe
		if(valDefaut)
		{
			valDefaut = parseFloat(valDefaut);
			somme += valDefaut;
		}
		
		champSomme.val(somme.toFixed(2));
		
		// Initialisation de la somme automatique au blur
		if(!col.hasClass('init_somme_ok')) col.initDSColChamps();
	});
};

/**
 * Calcul des sommes automatiquement
 *********************************************************/
$.fn.initDSColChamps = function()
{
	var col = $(this);
	var champs = col.find('input.num');
	var somme = 0;
	
	champs.blur(function()
	{
		col.majDSSommesCol();
	});
	
	col.addClass('init_somme_ok');
};

/**
 * Calcul de la somme d'une colonne
 *********************************************************/
$.fn.calculSommeCol = function()
{
	var col = $(this);
	var champs = col.find('input.num');
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
 * Met à jour les sommes des colonnes de stocks
 *********************************************************/
$.fn.majDSSommesCol = function()
{
	var col = $(this);
	var id = col.attr('id');
	var champsSommesAssoc = champsSommes.filter('[data-somme-col=#'+id+']')
	var somme = col.calculSommeCol();
	
	champsSommesAssoc.each(function()
	{
		var champSomme = $(this);
		var valDefaut = champSomme.attr('data-val-defaut');
		
		// Si une valeur par défaut existe
		if(valDefaut)
		{
			valDefaut = parseFloat(valDefaut);
			somme += valDefaut;
		}
		
		champSomme.val(somme);
		champSomme.verifNettoyageChamp();
	});
};

$.fn.ajaxPostForm = function(){
        var form = $(this);
        var form_id = $(this).attr('id');
        var inputs = $('#'+form_id+'.ajaxForm :input');
        $(inputs).each(function(){
                $(this).change(function(){
                    formPost(form);
            }); 
        });
            
        $('#'+form_id+' .ajax').each(function(){
                $(this).click(function(){
                    formPost(form);
            }); 
        });
    
}

var formPost = function(form)
{
        appDS.find('input.num').each(function(){
            $(this).verifNettoyageChamp();
        });
        
        $.ajax({
            url: $(form).attr('action'),
            type: "POST",
            data: $(form).serializeArray(),
            dataType: "json",
            async : true,
            success: function(msg){},  
            error: function(textStatus){  
                $( "#ajax_error").html(textStatus);
        }
    });
}

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

/**
 * Initalise la popup previsualisation de DS
 ******************************************/
var initValidDSPopup = function()
{
    $('#previsualiser').click(function() {
        openPopup($("#popup_loader"));
        $.ajax({
            url: ajax_url_to_print,
            success: function(data) {
                $('.popup-loading').empty();
                $('.popup-loading').css('background', 'none');
                $('.popup-loading').css('padding-top', '10px');
                $('.popup-loading').append('<p>Le PDF de votre déclaration de stock à bien été généré, vous pouvez maintenant le télécharger.<br /><br/><a href="'+data+'" class="telecharger-ds">Télécharger la DS</a></p>');
                openPopup($("#popup_loader"));

            }
        });
        return false;
    });
}
