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

var ajax_post_url = null;

$(document).ready(function()
{

    //navOngletsStock();
    initMsgAideDS();
    choixPrecDS();

    initLoadingCreationDS();

    if(!appDS.length) {

        return;
    }

    initDSSommesCol();
    initLieuxStockage();
    initLieuxStockageNeant();
    var ajaxForm = $('form.ajaxForm');
    if(ajaxForm.length > 0) {
        ajaxForm.ajaxPostForm();
    }
    initValidDSPopup();

    initConfirmeValidationDs();

    if ($('#validation_ds').length > 0) {
        $('#validation_ds').ready( function() {
            initValidationDs();
            initSendDSPopup();
        });
    }

    if ($('#confirmation_fin_stock').length > 0) {
        $('#confirmation_fin_stock').ready( function() {
            initValidationDs();
            initSendDSPopup();
        });
    }

    initExploitation();

	initStocks();

    initRecapStocks();

	scrollLieuxStockage();

    initDatepicker();


});


/**
 * Initialise les fonctions de la validation
 * de récolte
 ******************************************/
var initValidationDs = function(type)
{
    initValidDSPopup();
    initConfirmeValidationDs();
}

var initLoadingCreationDS = function()
{
    $('#form_ds #mon_espace_civa_valider[data-popup-loading=true]').bind('click', function() {
        //if($('#form_ds #type_ds_normal').attr('checked') == 'checked') {
          openPopup($("#popup_loader"));
        //}
    });
}

var initLieuxStockage = function()
{
    var checkboxes = $(".table_donnees input");
    checkboxes.each(function(){
        $(this).click(function(){
            majLieuNeant();
            });
    });
    $("#ds_lieu_neant").change(function(){
        if($(this).attr("readonly")){
            return false;
        }
        majCheckboxesAppellation();
    });

    $('#ds_lieux_stockage_toggle').click(function(){
        $("input[name='ds_lieu[ds_principale]']").removeAttr("style");
        $('#ds_lieux_stockage_toggle').remove();
    });

    $("input[name='ds_lieu[ds_principale]']").change(function(){
        $("#principal_label").remove();
        var numChecked = $("input[name='ds_lieu[ds_principale]']:checked").val();
        $("td.adresse_lieu").each(function(){
            $(this).removeClass("ds_lieu_principal_bold");
        })
        $("#adresse_"+numChecked).addClass("ds_lieu_principal_bold");
        $("#adresse_"+numChecked).append( "<span id='principal_label'>(principal)</span>" );
    });

};

var initExploitation = function()
{
    if($('#exploitation_administratif').length == 0)
    {

        return;
    }

    $('#btn_etape li.suiv a').focus();
};

// Donne le focus sur le premier select
var initStocks = function()
{
	if(appDS.find('#ds_add_produit_hashref').length > 0)
	{
		appDS.find('#ds_add_produit_hashref').focus();
	}
};

var initRecapStocks = function()
{
    if($('#recap_lieu_stockage').length == 0)
    {

        return;
    }

    $('#btn_etape li.suiv a').focus();
};

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
};

var majLieuNeant = function(){
    var one_checked = false;
    $(".table_donnees input").each(function(){
        if($(this).is(":checked")){
            one_checked = true;
        }
    });
    if(one_checked){
        $("#ds_lieu_neant").attr("readonly",true);
        initLieuxStockageNeant();
    }
    else
        $("#ds_lieu_neant").removeAttr("readonly");

};


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

        $('#'+form_id+' .ajax').each(function(){
                $(this).click(function(e){
                    ajax_post_url = $(this).attr('href');
                    formPost(form);
                    e.preventDefault()
            });
        });

};

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
            success: function(msg){if(ajax_post_url) {
                document.location.href=ajax_post_url;
            }},
            error: function(textStatus){
                form.submit();
            }
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

	// Scroll automatique sur les lieux de stockage s'ils existent
var scrollLieuxStockage = function()
{
	var listeLieuxStockage = $('#liste_lieux_stockage');

	if(listeLieuxStockage.length > 0)
	{
		$.scrollTo(listeLieuxStockage, 800);
	} else if($('#onglets_majeurs').length > 0) {
		$.scrollTo('#onglets_majeurs', 800);
	}
};

var choixPrecDS = function()
{
    $('#form_ds #mon_espace_civa_valider').click(function() {
        if($('#type_ds_suppr:checked').length > 0) {
            return confirm('Etes vous sûr(e) de vouloir supprimer cette déclaration ?');
        }
    });


};

/**
 * Initalise la popup previsualisation de DS
 ******************************************/
var initValidDSPopup = function()
{
    $('#previsualiserDS').click(function() {
        openPopup($("#popup_loader"));
        $.ajax({
            url: ajax_url_to_print,
            success: function(data) {
                $('.popup-loading').empty();
                $('.popup-loading').css('background', 'none');
                $('.popup-loading').css('padding-top', '10px');
                $('.popup-loading').append('<p>Le PDF de votre déclaration de stock à bien été généré, vous pouvez maintenant le télécharger.<br /><br/><a href="'+data+'" class="telecharger-ds" title="Télécharger la DS"></a></p>');
                openPopup($("#popup_loader"));

            }
        });
        return false;
    });
};

/* Confirmation de la validation */

var initConfirmeValidationDs = function()
{
    $('#valideDS').click(function() {
        openPopup($("#popup_confirme_validationDS"));
        return false;
    });
    $('#valideDS_OK').click(function() {
        $("#popup_confirme_validationDS").dialog('close');
        $("#principal").submit();
        return false;
    });
}

var initSendDSPopup = function()
{
    $('#btn-email').click(function() {
        openPopup($("#popup_confirme_mail"));
        return false;
    });
}


var initLieuxStockageNeant = function()
{
    $('#ds_lieu_neant').click(function() {
        var lien = $(this);

        if(lien.attr('readonly')){
            openPopup($("#popup_ds_neant"));
            return false;
        }
    });
}

var initDatepicker = function(){

    $(".datepicker").datepicker({
        showOn: "button",
        buttonImage: "/images/pictos/pi_calendrier.png",
        buttonImageOnly: true,
        dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
        monthNames: ["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Décembre"],
        dateFormat: 'dd/mm/yy',
        firstDay:1
});
};


/**
 * Messages d'aide
 ******************************************/
var initMsgAideDS = function()
{
    var liens = $('a.msg_aide_ds');
    var popup = $('#popup_msg_aide_ds');
  if(liens.length){
    liens.live('click', function()
    {
        var id_msg_aide = $(this).attr('rel');
        var title_msg_aide = $(this).attr('title');
	var url_doc = $(this).attr('doc');
        $(popup).html('<div class="ui-autocomplete-loading popup-loading"></div>');



        $.getJSON(
            url_ajax_msg_aide_ds,
            {
                id: id_msg_aide,
                url_doc: url_doc,
                title: title_msg_aide
            },
            function(json)
            {
                var titre = json.titre;
                var message = json.message;
                var url = json.url_doc;
                popup.html('<p></p>');
                popup.find('p').html(message);
                popup.dialog("option" , "title" , titre);
                popup.dialog("option" , "buttons" , {
                    telecharger: function() {
                        document.location.href = url
                        },
                    fermer: function() {
                        $(this).dialog( "close" );
                    }
                });
                $('.ui-dialog-buttonpane').find('button:contains("telecharger")').addClass('telecharger-btn');
                $('.ui-dialog-buttonpane').find('button:contains("fermer")').addClass('fermer-btn');
                $('.ui-dialog-buttonpane').find('button:contains("fermer")').focus();
                $('.ui-dialog-buttonpane').find('button:contains("telecharger")').focus();
            }
            );

        openPopup(popup);

        return false;
    });
  }
};
