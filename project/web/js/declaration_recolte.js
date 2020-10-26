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

    initMsgAide();
    $('#onglets_majeurs').ready( function() {
        initOngletsMajeurs();
    });
    $('#precedentes_declarations').ready( function() {
        accordeonPrecDecla();
    });
    $('#nouvelle_declaration').ready( function() {
        choixPrecDecla();
        accesGamma();
    });
    if ($('#exploitation_administratif').length > 0) {
        $('#exploitation_administratif').ready( function() {
            formExploitationAdministratif();
        });
    }
    if ($('#modification_compte').length > 0) {
        $('#modification_compte').ready( function() {
            formModificationCompte();
        });
    }

    $('.table_donnees').ready( function() {
        initTablesDonnes();
    });

    $('.gestion_recolte_donnees input').each(function(e)
    {
        var val = $(this).val();
        if(val.length > 12)
            $(this).addClass('num_alerte');
        else
            $(this).removeClass('num_alerte');
    });

    /* $('input.num').live('keyup',function(e)
    {
        alert($(this).val());
    });*/
    if ($('#exploitation_acheteurs').length > 0) {
        $('#exploitation_acheteurs').ready( function() {
            initTablesAcheteurs();
        });
    }

    if ($('#gestion_grands_crus').length > 0) {
        $('#gestion_grands_crus').ready( function() {
            initGestionGrandsCrus();
        });
    }

    if ($('#gestion_recolte.gestion_recolte_donnees').length > 0) {
        $('#gestion_recolte').ready( function() {
            initGestionRecolte();
            initGestionRecolteDonnees();
        });
    }

    if ($('#gestion_recolte.gestion_recolte_recapitulatif').length > 0) {
        $('#gestion_recolte').ready( function() {
            initGestionRecolte();
            initGestionRecolteRecapitulatif();
        });
    }

    if ($('#validation_dr').length > 0) {
        $('#validation_dr').ready( function() {
            initValidationDr();
            initSendDRPopup();
            initSendDRAcheteurPopup();
        });
    }

    if ($('#confirmation_fin_declaration').length > 0) {
        $('#confirmation_fin_declaration').ready( function() {
            initValidationDr();
            initSendDRPopup();
            initSendDRAcheteurPopup();
        });
    }

    var annee = new Date().getFullYear();

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

    $('.datepickerOpenOnLoad').focus();

   var btnInactif =  $(document).find('a.btn_inactif, input.btn_inactif');
   if(btnInactif.length){
     btnInactif.live('click', function() {
       return false;
     });

   }
   var btnClosePopup = $(document).find('a.close_popup');
   if(btnClosePopup.length){
      $(document).find('a.close_popup').live('click', function() {
          $('.popup_ajout').dialog('close');
          return false;
      });
    }

  var btnRendementsMax =   $(document).find('a.open_popup_rendements_max');
 if(btnRendementsMax.length){
    $(document).find('a.open_popup_rendements_max').live('click', function() {
        popup = $("#popup_rendements_max");

        $(popup).html('<div class="ui-autocomplete-loading popup-loading"></div>');

        popup.dialog
        ({
            autoOpen: false,
            draggable: false,
            resizable: false,
            width: 500,
            modal: true,
            buttons:{
                '': function() {
                    $(this).dialog( "close" );
                }
            }
        });

        $.get(
            url_ajax_rendements_max,
            function(data)
            {
                popup.html(data);
            }
        );

        openPopup(popup);
        popup.dialog('open');

        return false;
    });
  }
    if($('.col_active')){
        var element_premier_focus = $('.col_active').find('.premier_focus').eq(0);
        if(document.location.hash && $(document.location.hash).length > 0) {
            element_premier_focus = $(document.location.hash);
        }
        element_premier_focus.focus();
        element_premier_focus.select();
    }
    var btnVoirDrPrec = $(document).find('a.btn_voir_dr_prec');
    if(btnVoirDrPrec.length){
      btnVoirDrPrec.live('click', function() {
        openPopup($('#popup_dr_precedentes'));
        return false;
      });
    }

    if($('#popup_rappel_log')){
        openPopup($('#popup_rappel_log'));
        return false;
    }


});


/**
 * Onglets majeurs
 ******************************************/
var initOngletsMajeurs = function()
{
    var onglets = $('#onglets_majeurs');

    hauteurEgale(onglets.find('>li>a'));
    hauteurEgale(onglets.find('ul.sous_onglets li a'));
    if(onglets.hasClass('ui-tabs-nav')) $("#principal").tabs();
};

/**
 * Messages d'aide
 ******************************************/
var initMsgAide = function()
{
    var liens = $('a.msg_aide');
    var popup = $('#popup_msg_aide');
    if(liens.length){
    liens.live('click', function()
    {
        var id_msg_aide = $(this).attr('rel');
        var title_msg_aide = $(this).attr('title');
	      var url_doc = $(this).attr('doc');
        $(popup).html('<div class="ui-autocomplete-loading popup-loading"></div>');


        $.getJSON(
            url_ajax_msg_aide,
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
        if(type_decla.filter(':checked').val() == 'vierge' || type_decla.filter(':checked').val() == 'import' || type_decla.filter(':checked').val() == 'visualisation_avant_import') liste_prec_decla.hide();
        else liste_prec_decla.show();
    });

    var espaceCivaValider = $('#mon_espace_civa_valider');
    if(espaceCivaValider.length){

      $('#mon_espace_civa_valider').live('click', function() {
        if($('#type_declaration_suppr:checked').length > 0)
        return confirm('Etes vous sûr(e) de vouloir supprimer cette déclaration ?');
      });
    }


};

/**
 * Bloc Mon espace Alsace Gamm@
 ******************************************/
var accesGamma = function()
{
    $('#mon_espace_civa_gamma_valider').bind('click', function() {
        var choix = $('#form_gamma input[name="gamma[type_acces]"]:checked');
        if (choix.val() == 'plateforme_test') {
          openPopup($("#popup_loader"));
        } else if(choix.val() == 'inscription') {
          openPopupWithoutButton($("#popup_inscription_gamma"));
          return false;
        }

    });
};


/**
 * Accordéon précédentes déclarations
 ******************************************/
var accordeonPrecDecla = function()
{
/* $('#precedentes_declarations ul.ui-accordion').accordion(
    {
        autoHeight: false,
        active: 0
    });*/
};

/**
 * Formulaire de modification des infos
 * de l'exploitation
 ******************************************/
var formExploitationAdministratif = function()
{
    var blocs = $('#infos_exploitation, #gestionnaire_exploitation');
    var btns_modifier = blocs.find('a.modifier');

    blocs.each(function()
    {
        var bloc = $(this);
        var presentation_infos = bloc.find('.presentation');
        var modification_infos = bloc.find('.modification');
        var btn = bloc.find('.btn');
        var btn_modifier = btn.find('a.modifier');
        var btn_annuler = btn.find('a.annuler');

        btn_modifier.click(function()
        {
            presentation_infos.hide();
            modification_infos.show();
            btns_modifier.hide();
            $('#btn_etape input[name=boutons[next]]').addClass('btn_inactif');
            $('#btn_etape input[name=boutons[previous]]').addClass('btn_inactif');
            $('#btn_suivant').addClass('btn_inactif');
            $('#btn_precedent').addClass('btn_inactif');

            bloc.addClass('edition');
            bloc.find('form input[type!="hidden"], form select').first().focus();

            return false;
        });

        if(bloc.hasClass('edition')) {
            btn_modifier.click();
        }
    });
};

/**
 * Formulaire de modification des identifiants
 ******************************************/
var formModificationCompte = function()
{
    var bloc = $('#modification_compte');

    var presentation_infos = bloc.find('.presentation');
    var modification_infos = bloc.find('.modification');
    var btn = bloc.find('.btn');
    var btn_modifier = btn.find('a.modifier');
    var btn_annuler = btn.find('a.annuler');

    // modification_infos.hide();

    btn_modifier.click(function()
    {
        presentation_infos.hide();
        modification_infos.show();
        $("a.modifier").hide();
        bloc.addClass('edition');
        return false;
    });

    btn_annuler.click(function()
    {
        presentation_infos.show();
        modification_infos.hide();
        $("a.modifier").show();
        bloc.removeClass('edition');
        return false;
    });

};

/**
 * Initialise les fonctions des tables
 * de données
 ******************************************/
var initTablesDonnes = function()
{
    var tables = $('table.pyjama_auto');

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
    $('form input[type!="hidden"], form select').first().focus();

    var tables_acheteurs = $('#exploitation_acheteurs table.tables_acheteurs');

    tables_acheteurs.each(function()
    {
        var table_achet = $(this);

        var bloc = table_achet.parent();
        var form_ajout = bloc.next(".form_ajout");
        var btn_ajout = bloc.children('.btn');

        if(bloc.attr('id') != 'vol_sur_place')
        {
            toggleTrVide(table_achet);
            supprimerLigneTable(table_achet, form_ajout);

            initTableAjout(table_achet, form_ajout, btn_ajout);
            masquerTableAjout(table_achet, form_ajout, 0);

            btn_ajout.children('a.ajouter').click(function()
            {
                afficherTableAjout(table_achet, form_ajout, btn_ajout);
                return false;
            });
        }
    });

    $('body').live('keypress', (function (e) {
        if ($('#exploitation_acheteurs .form_ajout').find('a.valider:visible').length > 0 && e.which == 13) {
            $('#exploitation_acheteurs .form_ajout').find('a.valider:visible').click();
            return false;
        }
        return true;
    }));

    initPopup($('#btn_etape li.prec input, #btn_etape li.suiv input'), $('#popup_msg_erreur'),
        function() {
            return (tables_acheteurs.find('input[type=checkbox]:checked').length < 1);
        }
        );


    $('input[name="exploitation_acheteurs[lies_saisis_cepage]"]').change(function() {
        $("#exploitation_acheteurs #choix_mode_saisie_lies div#bloc_choix_lies div").removeClass('actif');
        $(this).parent().addClass('actif');
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
var supprimerLigneTable = function(table_achet, form_ajout)
{
    var btn = table_achet.find('tbody tr a.supprimer');

    btn.live('click', function()
    {
        var choix = confirm('Confirmez-vous la suppression de cette ligne ?');
        if(choix)
        {
            reinsertAcheteurFromAutocompletion($(this).parents('tr').find('td.cvi').html(), form_ajout);

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
    var source_autocompletion_using = eval(table_ajout.attr('rel')+'_using');
    var champs = table_ajout.find('input');
    var nom = table_ajout.find('td.nom input[type=text]');
    var nom_hidden = table_ajout.find('td.nom span');
    var cvi = table_ajout.find('td.cvi');
    var commune = table_ajout.find('td.commune');
    var btn = form_ajout.find('.btn a');
    var acheteur_mouts = 0;
    var qualite_name = '';

    nom.autocomplete(
	{
        minLength: 0,
        source: source_autocompletion,
        focus: function(event, ui)
        {
            nom.val(ui.item[0]);
            nom_hidden.val(ui.item[0]);
            cvi.find('span').text(ui.item[1]);
            cvi.find('input').val(ui.item[1]);
            commune.find('span').text(ui.item[2]);
            commune.find('input').val(ui.item[2]);

            return false;
        },
        select: function(event, ui)
        {
            nom.val(ui.item[0]);
            nom_hidden.text(ui.item[0]);
            cvi.find('span').text(ui.item[1]);
            cvi.find('input').val(ui.item[1]);
            commune.find('span').text(ui.item[2]);
            commune.find('input').val(ui.item[2]);

            return false;
        }
    });

    nom.change( function () {
        if (cvi.find('input').val() != '' && nom.val() != nom_hidden.text()) {
            nom.val('');
            nom_hidden.text('');
            cvi.find('span').text('');
            cvi.find('input').val('');
            commune.find('span').text('');
            commune.find('input').val('');
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

        qualite_name = form_ajout.attr('rel');

        if($(this).hasClass('valider'))
        {
            if(cvi.find('input').val() == '')
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

                $.post(url_ajax,
                {
                    action: "ajout_ligne_table",
                    donnees: donnees,
                    acheteur_mouts: acheteur_mouts,
                    qualite_name: qualite_name
                },
                function(data)
                {
                    var tr = $(data);
                    tr.appendTo(table_achet);
                    toggleTrVide(table_achet);
                    styleTables(table_achet);

                    deleteAcheteurFromAutocompletion(tr.find('td.cvi').html(), form_ajout);
                });
            }
        }

        masquerTableAjout(table_achet, form_ajout, 1);
        btn_ajout.show();

        return false;
    });
};

/**
 * Supprime un CVI de la liste autocomplete
 *
 ******************************************/
var deleteAcheteurFromAutocompletion = function(cvi, form_ajout)
{
    var table_ajout = form_ajout.find('table');
    var source_autocompletion = eval(table_ajout.attr('rel'));
    var source_autocompletion_using = eval(table_ajout.attr('rel')+'_using');
    var nom = table_ajout.find('td.nom input');

    for(var acheteur_id in source_autocompletion) {
        var tab = source_autocompletion[acheteur_id].split('|@');
        if (tab[1] == cvi) {
            source_autocompletion_using.unshift(source_autocompletion[acheteur_id]);
            source_autocompletion.splice(acheteur_id, 1);
            break;
        }
    }

    nom.autocomplete('option', 'source', source_autocompletion);
}

/**
 * Ajoute un CVI dans la liste autocomplete
 *
 ******************************************/
var reinsertAcheteurFromAutocompletion = function(cvi, form_ajout)
{
    var table_ajout = form_ajout.find('table');
    var source_autocompletion = eval(table_ajout.attr('rel'));
    var source_autocompletion_using = eval(table_ajout.attr('rel')+'_using');
    var nom = table_ajout.find('td.nom input');

    for(var acheteur_id in source_autocompletion_using) {
        var tab = source_autocompletion_using[acheteur_id].split('|@');
        if (tab[1] == cvi) {
            source_autocompletion.unshift(source_autocompletion_using[acheteur_id]);
            source_autocompletion_using.splice(acheteur_id, 1);
            break;
        }
    }
    nom.autocomplete('option', 'source', source_autocompletion);
}

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

    champs_txt.attr('value','');
    champs_cb.attr('checked','');
    champs_cb.filter('.cremant_alsace').attr('checked','checked');
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
    etatChampsTableAcht('disabled');
    form_ajout.find('input[type="text"]').focus();
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
    var btns_etape = $('#btn_etape input');

    if(type == 'disabled')
    {
        champs.attr('disabled', 'disabled');
        btns_supprimer.hide();
        btns.hide();
        btns_etape.addClass('btn_inactif');
    }
    else
    {
        champs.attr('disabled', '');
        btns_supprimer.show();
        btns.show();
        btns_etape.removeClass('btn_inactif');
    }
};


/**
 * Initialise les fonctions de la validation
 * de récolte
 ******************************************/
var initValidationDr = function(type)
{
    initValidDRPopup();
    initConfirmeValidationDr();
}


/**
 * Initalise la popup previsualisation de DR
 ******************************************/
var initValidDRPopup = function()
{
    $('#previsualiser').click(function() {
        openPopup($("#popup_loader"));
        $.ajax({
            url: ajax_url_to_print,
            success: function(data) {
                $('.popup-loading').empty();
                $('.popup-loading').css('background', 'none');
                $('.popup-loading').css('padding-top', '10px');
                $('.popup-loading').append('<p>Le PDF de votre déclaration de récolte à bien été généré, vous pouvez maintenant le télécharger.<br /><br/><a href="'+data+'" class="telecharger-dr"></a></p>');
                openPopup($("#popup_loader"));

            }
        });
        return false;
    });
}

/**
 * Initalise la popup d'envoie par mail de la DR
 ******************************************/

var initSendDRPopup = function()
{
    $('#btn-email').click(function() {
        openPopup($("#popup_confirme_mail"));
        return false;
    });
}

/**
 * Initalise la popup d'envoie par mail de la DR
 ******************************************/

var initSendDRAcheteurPopup = function()
{
    $('#btn-email-acheteur').click(function() {
        openPopup($("#popup_confirme_mail_acheteur"));
        return false;
    });
}


/* Confirmation de la validation */

var initConfirmeValidationDr = function()
{
    $('#valideDR').click(function() {
        openPopup($("#popup_confirme_validation"));
        return false;
    });
    $('#valideDR_OK').click(function() {
        $("#popup_confirme_validation").dialog('close');
        $("#principal").submit();
        return false;
    });
}


/**
 * Initialise les fonctions des tables
 * d'acheteurs
 ******************************************/
var initGestionGrandsCrus = function()
{
    hauteurEgale('p.intro_declaration');
    initPopup($('#btn_etape li.prec input, #btn_etape li.suiv input'), $('#popup_msg_erreur'),
        function() {
            return ($('ul#liste_grands_crus').find('li').length < 1);
        }
        );
};

/**
     * Initialise les fonctions de la gestion
     * de récolte
     ******************************************/
var initGestionRecolte = function()
{
    initDRPopups();
}

/**
     * Initialise les fonctions de la gestion
     * de récolte des données
     ******************************************/
var initGestionRecolteDonnees = function()
{
    etatBtnAjoutCol();

    hauteurEgaleColRecolte();
    largeurColScrollerCont();
    $('span.ombre').height($('#col_scroller').height()-15);

    etatBtnRecolteCanBeInactif(true);

    $('.col_recolte.col_active input').live('change', function() {
        var val = $(this).val();
        $(this).val(val.replace(',', '.'));

        etatBtnRecolteCanBeInactif(false);

        if ($(this).hasClass('volume') || $(this).hasClass('lies') || $(this).hasClass('vci')) {
            volumeOnChange(this);
        }
        if ($(this).attr('id') == 'detail_superficie') {
            superficieOnChange(this)
        }
        if ($(this).attr('id') == 'detail_denomination') {
            $(this).val(jQuery.trim($(this).val()));
        }
    });

    $('.col_recolte.col_active select').live('change', function () {
        etatBtnRecolteCanBeInactif(false);
    });

    $('.col_recolte.col_active form input[type!="hidden"], col_recolte.col_active form select').first().focus();

    $('.col_recolte.col_validee input[type="text"]').click(function() {
        var lien_modifier = $(this).parents('.col_recolte').find('.col_btn a.modifier_tmp')[0];

        if($(lien_modifier).attr('disabled')) {
            return;
        }

        lien_modifier.href += '#' + $(this).attr('data-form-input-id');

        lien_modifier.click();
    });
};

/**
     * Initialise la page recapitulatif de la recolte
     ******************************************/

var initGestionRecolteRecapitulatif = function() {
    $('a.btn_recolte_can_be_inactif').addClass('btn_inactif');
    $('#recapitulatif_lieu_lies').focus();
}

var etatBtnRecolteCanBeInactif = function (actif) {
    if (actif) {
        $('a.btn_recolte_can_be_inactif').removeClass('btn_inactif');
        $('.col_recolte.col_active .col_btn a.annuler_tmp').addClass('btn_inactif');
    } else {
        $('a.btn_recolte_can_be_inactif').addClass('btn_inactif');
        $('.col_recolte.col_active .col_btn a.annuler_tmp').removeClass('btn_inactif');
    }
    etatBtnAjoutCol();
}

var updateElementRows = function (inputObj, totalObj) {
    totalObj.val(0);
    inputObj.each(function() {
        var total = parseFloat(totalObj.val());
        var element = parseFloat($(this).val());
        total += element;
        if (element && parseFloat(totalObj.val()) != total) {
            totalObj.val(parseFloat(truncTotal(total)));
        }
    });
};
var updateAppellationTotal = function (cepageCssId, appellationCssId) {
    var app_orig = parseFloat($(appellationCssId+'_orig').val());
    if (!app_orig)
        app_orig = 0;
    var cep_orig = parseFloat($(cepageCssId+'_orig').val());
    if (!cep_orig)
        cep_orig = 0;
    var cep_now  = parseFloat($(cepageCssId).val());
    if (!cep_now)
        cep_now = 0;
    $(appellationCssId).val(truncTotal(app_orig - cep_orig + cep_now));
}
var superficieOnChange = function(input) {
    if (!input) {
        input = this;
    }

    var rendement_appellation = parseFloat($('#appellation_rendement').val());

    updateElementRows($('input.superficie'), $('#cepage_total_superficie'));
    updateAppellationTotal('#cepage_total_superficie', '#appellation_total_superficie');
    $('#detail_max_volume').val(parseFloat($('#detail_superficie').val())/100 * parseFloat($('#detail_rendement').val()));
    $('#appellation_max_volume').val(parseFloat($('#appellation_total_superficie').val())/100 * parseFloat($('#appellation_rendement').val()));


    if ($('#cepage_rendement').val() != -1) {
        $('#cepage_max_volume').val(parseFloat($('#cepage_total_superficie').val())/100 * parseFloat($('#cepage_rendement').val()));
    }
    volumeOnChange(input);
};

var updateRevendiqueDPLC = function (totalRecolteCssId, elementCssId) {
    if($(elementCssId+'_dplc_rendement').length > 0) {
    	 dplc_rendement = parseFloat($(totalRecolteCssId).val()) - parseFloat($(elementCssId+'_max_volume').val());

	    if(dplc_rendement > 0) {
	        $(elementCssId+'_dplc_rendement').val(dplc_rendement);
	    } else {
	        $(elementCssId+'_dplc_rendement').val(0);
	    }

    	$(elementCssId+'_volume_dplc').val($(elementCssId+'_dplc_rendement').val());
	}

    if($(elementCssId+'_total_dplc_sum').length > 0 && $(elementCssId+'_dplc_rendement').length == 0) {
    	$(elementCssId+'_volume_dplc').val(parseFloat($(elementCssId+'_total_dplc_sum').val().replace('Σ ', '')));
    }

    if($(elementCssId+'_total_dplc_sum').length > 0 && $(elementCssId+'_dplc_rendement').length > 0 && $(elementCssId+'_total_dplc_sum').val() > $(elementCssId+'_dplc_rendement').val()) {
        $(elementCssId+'_volume_dplc').val(parseFloat($(elementCssId+'_total_dplc_sum').val().replace('Σ ', '')));
    }

    var vci = 0;
    if($(elementCssId+'_vci').length > 0) {
        vci = parseFloat($(elementCssId+'_vci').val());
    }

    var dplc = parseFloat($(elementCssId+'_volume_dplc').val());
    if(!dplc) {
        dplc = 0;
    }
    var dplc_with_vci = dplc - vci;
    var usages_industriels = 0;
    var lies = parseFloat($(elementCssId+'_lies').val());
    if(!lies) {
        lies = 0;
    }

    if(dplc_with_vci > lies) {
        usages_industriels = dplc_with_vci;
        $(elementCssId+'_volume_revendique').val(parseFloat($(totalRecolteCssId).val()) - dplc);
    } else {
        usages_industriels = lies;
        $(elementCssId+'_volume_revendique').val(parseFloat($(totalRecolteCssId).val()) - usages_industriels);
    }

    $(elementCssId+'_usages_industriels').val(usages_industriels);
};

var addClassAlerteIfNeeded = function (inputObj, condition, css_class)
{
    if(!css_class) {
        css_class = 'alerte';
    }

    inputObj.removeClass(css_class);
    if (condition) {
        inputObj.addClass(css_class);
    }
};

var volumeOnChange = function(input) {
    if (!input) {
        input = this;
    }

    updateElementRows($('input.volume'), $('#detail_vol_total_recolte'));
    updateRevendiqueDPLC('#detail_vol_total_recolte', '#detail');

    if (!autoTotal) {
        return ;
    }

    $('ul.acheteurs li').each(function () {
        var css_class = $(this).attr('class');
        updateElementRows($('#col_scroller input.'+css_class), $('#col_cepage_total input.'+css_class));
        updateAppellationTotal('#col_cepage_total input.'+css_class, '#col_couleur_totale input.'+css_class);
        updateAppellationTotal('#col_cepage_total input.'+css_class, '#col_recolte_totale input.'+css_class);
    });

    updateElementRows($('input.cave'), $('#cepage_total_cave'));
    updateElementRows($('input.total'), $('#cepage_total_volume'));
    updateElementRows($('input.lies'), $('#cepage_lies'));
    updateElementRows($('input.vci'), $('#cepage_vci'));

    if ($('#cepage_rendement').val() == -1) {
        $('#cepage_max_volume').val(parseFloat($('#cepage_total_volume').val()));
    }
    updateRevendiqueDPLC('#cepage_total_volume', '#cepage');

    updateAppellationTotal('#cepage_total_cave', '#appellation_total_cave');
    updateAppellationTotal('#cepage_total_volume', '#appellation_total_volume');
    updateAppellationTotal('#cepage_volume_revendique', '#appellation_total_revendique_sum');

    if($('#appellation_lies').attr('mode') == 'sum') {
        updateAppellationTotal('#cepage_lies', '#appellation_lies');
    }

    updateAppellationTotal('#cepage_volume_dplc', '#appellation_total_dplc_sum');
    updateAppellationTotal('#cepage_vci', '#appellation_vci');

    updateRevendiqueDPLC('#appellation_total_volume', '#appellation');

    addClassAlerteIfNeeded($('#appellation_volume_revendique'), parseFloat($('#appellation_volume_dplc').val()) > 0, 'rouge');
    addClassAlerteIfNeeded($('#appellation_usages_industriels'), parseFloat($('#appellation_volume_dplc').val()) > 0, 'rouge');
    addClassAlerteIfNeeded($('#appellation_dplc_rendement'), parseFloat($('#appellation_dplc_rendement').val()) > 0, 'rouge');
    addClassAlerteIfNeeded($('#appellation_dplc_rendement'), parseFloat($('#appellation_dplc_rendement').val()) > 0 && parseFloat($('#appellation_dplc_rendement').val()) == parseFloat($('#appellation_volume_dplc').val()), 'alerte');
    addClassAlerteIfNeeded($('#appellation_total_dplc_sum'), parseFloat($('#appellation_total_dplc_sum').val()) > 0, 'rouge');
    addClassAlerteIfNeeded($('#appellation_total_dplc_sum'), parseFloat($('#appellation_total_dplc_sum').val()) > 0 && parseFloat($('#appellation_total_dplc_sum').val()) == parseFloat($('#appellation_volume_dplc').val()), 'alerte');
    addClassAlerteIfNeeded($('#cepage_volume_revendique'), parseFloat($('#cepage_volume_dplc').val()) > 0, 'rouge');
    addClassAlerteIfNeeded($('#cepage_usages_industriels'),parseFloat($('#cepage_volume_dplc').val()) > 0, 'rouge');
    addClassAlerteIfNeeded($('#cepage_dplc_rendement'), parseFloat($('#cepage_dplc_rendement').val()) > 0, 'rouge');
    addClassAlerteIfNeeded($('#cepage_dplc_rendement'), parseFloat($('#cepage_dplc_rendement').val()) > 0 && parseFloat($('#cepage_dplc_rendement').val()) == parseFloat($('#cepage_volume_dplc').val()), 'alerte');

    $('#appellation_total_dplc_sum').val('Σ '+parseFloat(truncTotal($('#appellation_total_dplc_sum').val())).toFixed(2));
    $('#appellation_total_revendique_sum').val('Σ '+parseFloat(truncTotal($('#appellation_total_revendique_sum').val())).toFixed(2));

    var val = parseFloat($('#appellation_total_volume').val())+'';
    if (parseFloat($('#appellation_total_superficie').val()) > 0) {
        var val = ((parseFloat($('#appellation_total_volume').val()) - (parseFloat($('#appellation_lies').val()))) / (parseFloat($('#appellation_total_superficie').val()) / 100))+'';
    }
    $('#appellation_current_rendement').html(Math.round(val));
    val = parseFloat($('#cepage_total_volume').val())+'';
    if (parseFloat($('#cepage_total_superficie').val()) > 0) {
        val = ((parseFloat($('#cepage_total_volume').val()) - (parseFloat($('#cepage_lies').val()))) / (parseFloat($('#cepage_total_superficie').val()/100)))+'';
    }
    $('#cepage_current_rendement').html(Math.round(val));

    var rendement_appellation = parseFloat($('#appellation_rendement').val());
    var rendement_vci_max = 0;
    var rendement_recoltant = parseFloat($('#appellation_current_rendement').html());
    var rendement_config_vci = parseFloat($('#appellation_rendement_vci').val());
    var rendement_excedent = rendement_recoltant - rendement_appellation;
    if(rendement_config_vci && rendement_excedent > rendement_config_vci) {
        rendement_vci_max = rendement_config_vci;
    } else if(rendement_config_vci && rendement_excedent > 0) {
        rendement_vci_max = rendement_excedent;
    }
    var rendement_appellation_max = rendement_appellation;
    $('#appellation_max_rendement').val(rendement_appellation_max);

    var rendement_cepage = parseFloat($('#cepage_rendement').val());
    var rendement_vci_max = 0;
    var rendement_recoltant = parseFloat($('#cepage_current_rendement').html());
    var rendement_config_vci = parseFloat($('#cepage_rendement_vci').val());
    var rendement_excedent = rendement_recoltant - rendement_cepage;
    if(rendement_config_vci && rendement_excedent > rendement_config_vci) {
        rendement_vci_max = rendement_config_vci;
    } else if(rendement_config_vci && rendement_excedent > 0) {
        rendement_vci_max = rendement_excedent;
    }
    var rendement_cepage_max = rendement_cepage;
    $('#cepage_max_rendement').val(rendement_cepage_max);

    if($('#appellation_vci').length > 0) {
        addClassAlerteIfNeeded($('#appellation_vci'), $('#appellation_vci').val() > Math.round((parseFloat($('#appellation_max_rendement').val()) - parseFloat($('#appellation_rendement').val())) * parseFloat($('#appellation_total_superficie').val() / 100) * 100) / 100, 'rouge');
    }

    if($('#cepage_vci').length > 0) {
        console.log(Math.round((parseFloat($('#cepage_max_rendement').val()) - parseFloat($('#cepage_rendement').val())) * parseFloat($('#cepage_total_superficie').val() / 100) * 100) / 100);
        console.log($('#cepage_max_rendement').val());
        console.log($('#cepage_rendement').val());
        console.log($('#cepage_total_superficie').val());
        addClassAlerteIfNeeded($('#cepage_vci'), $('#cepage_vci').val() > Math.round((parseFloat($('#cepage_max_rendement').val()) - parseFloat($('#cepage_rendement').val())) * parseFloat($('#cepage_total_superficie').val() / 100) * 100) / 100, 'rouge');
    }

    addClassAlerteIfNeeded($('#donnees_recolte_sepage .rendement'), parseFloat($('#cepage_current_rendement').html()) > parseFloat($('#cepage_max_rendement').val()), 'rouge');
    addClassAlerteIfNeeded($('#col_recolte_totale .rendement'), parseFloat($('#appellation_current_rendement').html()) > parseFloat($('#appellation_max_rendement').val()), 'rouge');
    addClassAlerteIfNeeded($('#col_couleur_totale .rendement'), parseFloat($('#appellation_current_rendement').html()) > parseFloat($('#appellation_rendement').val()), 'rouge');

    $('.num').each(function() { $(this).verifNettoyageChamp()});
};

var truncTotal = function (val) {
    return trunc(val, 2);
}

var trunc = function(what,howmuch) {
    what = ''+what;
    if (what.indexOf('.') == -1) return what;
    pos = what.indexOf('.')+howmuch+1;
    return what.slice(0,pos);
}

/**
     * Egalise les hauteurs des colonnes
     ******************************************/
var hauteurEgaleColRecolte = function()
{
    var col_intitules = '#colonne_intitules';

    hauteurEgaleLignesRecolte(col_intitules+' p', 'p');
    hauteurEgaleLignesRecolte(col_intitules+' li', 'li');
    $(col_intitules + ', #col_scroller .col_recolte .col_cont, #gestion_recolte .col_total .col_cont').height('auto');
    hauteurEgale(col_intitules + ', #col_scroller .col_recolte .col_cont, #gestion_recolte .col_total .col_cont');
};

var hauteurEgaleLignesRecolte = function(intitule, elem)
{
    var col_recolte = '#col_scroller .col_recolte';
    var col_total = '#gestion_recolte .col_total'

    $(intitule).each(function(i)
    {
        var s = intitule+':eq('+i+')';

        $(col_recolte).each(function(j)
        {
            s += ', '+col_recolte+':eq('+j+') .col_cont '+elem+':eq('+i+')';
        });

        $(col_total).each(function(j)
        {
            s += ', '+col_total+':eq('+j+') .col_cont '+elem+':eq('+i+')';
        });

        hauteurEgale(s);
    });
};

/**
     * Etat du bouton d'ajout de colonne
     ******************************************/
var etatBtnAjoutCol = function()
{
    var col_recolte = $('#col_scroller .col_recolte');
    var btn = $('a#ajout_col');

    if(col_recolte.filter('.col_active').size() > 0 && !$('.col_recolte.col_active .col_btn a.annuler_tmp').hasClass('btn_inactif')) btn.addClass('btn_inactif');
    else btn.removeClass('btn_inactif');
};

/**
     * Largeur colonne scroll conteneur
     ******************************************/
var largeurColScrollerCont = function()
{
    var cont = $('#col_scroller_cont');
    var cols = cont.find('.col_recolte');
    var col_active = cont.find('.col_active');
    var btn = cont.find('a#ajout_col');

    var largeur = btn.width();

    cols.each(function()
    {
        largeur += $(this).width() + parseInt($(this).css('marginRight'));
    });

    cont.width(largeur);

    if(col_active.size() > 0)
        cont.parent().scrollTo(col_active, 0 );
    else
        cont.parent().scrollTo( {
            top: 0,
            left: largeur
        }, 0 );
};



/**
     * Initalise les popups de DR
     ******************************************/
var initDRPopups = function()
{
    var onglets = $('#onglets_majeurs');
    var btn_ajout_appelation = onglets.find('li.ajouter_appelation a');
    var btn_ajout_lieu = onglets.find('li.ajouter_lieu a');
    var col_recolte_cont = $('#col_scroller_cont');
    var btn_ajout_acheteur = col_recolte_cont.find('a.ajout_acheteur');
    var btn_ajout_cave = col_recolte_cont.find('a.ajout_cave');
    var btn_ajout_mout = col_recolte_cont.find('a.ajout_mout');
    var btn_ajout_motif = col_recolte_cont.find('a.ajout_motif');

    var popup_ajout_appelation = $('#popup_ajout_appelation');
    var popup_ajout_lieu = $('#popup_ajout_lieu');
    var popup_ajout_acheteur = $('#popup_ajout_acheteur');
    var popup_ajout_cave = $('#popup_ajout_cave');
    var popup_ajout_mout = $('#popup_ajout_mout');
    var popup_ajout_motif = $('#popup_ajout_motif');

    var config_default = {
        ajax: false,
        auto_open: false
    };
    initPopupAjout(btn_ajout_appelation, popup_ajout_appelation, config_default);
    initPopupAjout(btn_ajout_lieu, popup_ajout_lieu, config_default);

    if (popup_ajout_acheteur.length > 0 && popup_ajout_cave.length > 0 && popup_ajout_mout.length > 0 && popup_ajout_motif.length > 0) {
        initPopupAjout(btn_ajout_acheteur, popup_ajout_acheteur,config_default, var_liste_acheteurs, var_liste_acheteurs_using);
        initPopupAjout(btn_ajout_cave, popup_ajout_cave, config_default, var_liste_caves, var_liste_caves_using);
        initPopupAjout(btn_ajout_mout, popup_ajout_mout, config_default, var_liste_acheteurs_mouts, var_liste_acheteurs_mouts_using);
        initPopupAjout(btn_ajout_motif, popup_ajout_motif, var_config_popup_ajout_motif);
    }

};

var initPopupAjout = function(btn, popup, config, source_autocompletion, source_autocompletion_using)
{
    popup.dialog
    ({
        autoOpen: false,
        draggable: false,
        resizable: false,
        width: 375,
        modal: true,
        buttons:{
            '': function() {

                $(this).dialog( "close" );
            }
        }
    });

    btn.live('click', function()
    {
        if(config.ajax == true) {
            loadContentPopupAjax(popup, btn.attr('href'), config);
        }
        popup.dialog('open');
        return false;
    });
    if (config.auto_open == true) {
        loadContentPopupAjax(popup, config.auto_open_url, config);
        popup.dialog('open');
    }
    if(source_autocompletion) initPopupAutocompletion(popup, source_autocompletion, source_autocompletion_using);
};

var loadContentPopupAjax = function(popup, url, config)
{
    $(popup).html('<div class="ui-autocomplete-loading popup-loading"></div>');
    $(popup).load(url);
}

var initPopupAutocompletion = function(popup, source_autocompletion, source_autocompletion_using)
{
    var nom = popup.find('input.nom');
    var nom_hidden = popup.find('span.nom_hidden');
    var cvi = popup.find('input.cvi');
    var commune = popup.find('input.commune');
    var type_cssclass = popup.find('input[name=type_cssclass]').val();
    var type_name_field = popup.find('input[name=type_name_field]').val();
    var btn = popup.find('input[type=image]');
    var btn_loading = popup.find('.valider-loading');
    var form = popup.find('form');

    /*form.submit(function() {
        btn.click();
        return false;
    })*/

    $(popup).bind( "dialogclose", function(event, ui) {
        nom.val('');
        cvi.val('');
        commune.val('');
    });

    nom.autocomplete(
    {
        minLength: 0,
        source: source_autocompletion,
        focus: function(event, ui)
        {
            nom.val(ui.item[0]);
            nom_hidden.text(ui.item[0]);
            cvi.val(ui.item[1]);
            commune.val(ui.item[2]);
            return false;
        },
        select: function(event, ui)
        {
            nom.val(ui.item[0]);
            nom_hidden.text(ui.item[0]);
            cvi.val(ui.item[1]);
            commune.find('input').val(ui.item[2]);
            return false;
        }
    });

    nom.change( function () {
        if (cvi.val() != '' && nom.val() != nom_hidden.text()) {
            nom.val('');
            nom_hidden.text('');
            cvi.val('');
            commune.val('');
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
        if(cvi.val()=='')
        {
            alert("Veuillez renseigner le nom de l'acheteur");
            return false;
        }
        else
        {
            btn.hide();
            btn_loading.show();
            btn_loading.css('display', 'inline-block');
            $.post(form.attr('action'),
            {
                cvi: cvi.val(),
                form_name: type_name_field
            },
            function(data)
            {
                var cvi_val = cvi.val();
                var html_header_item = $('#acheteurs_header_empty').clone();
                var html_acheteur_item = $('#acheteurs_item_empty').clone();
                var css_class_acheteur = 'acheteur_' + type_name_field + '_' + cvi_val;

                html_header_item.find('li').
                html(nom.val()).
                addClass(css_class_acheteur);

                html_acheteur_item.find('input').addClass(css_class_acheteur);

                if (!($('#colonne_intitules, .col_recolte').find('.'+type_cssclass+' ul').length > 0)) {
                    $('#colonne_intitules, .col_recolte').find('.'+type_cssclass).append('<ul></ul>');
                    $('#colonne_intitules').find('.'+type_cssclass+' ul').addClass('acheteurs');
                }


                $('#colonne_intitules').
                find('.'+type_cssclass+' ul').
                append(html_header_item.html());

                $('.col_recolte.col_validee, .col_recolte.col_cepage_total, .col_recolte.col_total').
                find('.'+type_cssclass+' ul').
                append(html_acheteur_item.html());

                $('.col_recolte.col_active').
                find('.'+type_cssclass+' ul').
                append(data);

                popup.dialog('close');
                btn.show();
                btn_loading.hide();
                hauteurEgaleColRecolte();
                deleteRecolteAcheteurFromAutocompletion(cvi_val, nom, source_autocompletion, source_autocompletion_using);
            });

            return false;
        }

    });
};


/**
 * Supprime un CVI de la liste autocomplete de la recolte
 *
 ******************************************/
var deleteRecolteAcheteurFromAutocompletion = function(cvi, champ_autocompletion, source_autocompletion, source_autocompletion_using)
{
    for(var acheteur_id in source_autocompletion) {
        var tab = source_autocompletion[acheteur_id].split('|@');
        if (tab[1] == cvi) {
            source_autocompletion_using.unshift(source_autocompletion[acheteur_id]);
            source_autocompletion.splice(acheteur_id, 1);
            break;
        }
    }

    champ_autocompletion.autocomplete('option', 'source', source_autocompletion);
}

/**
     * Initialise une popup
     ******************************************/
var openPopup = function(popup, fn_open_if) {

    popup.dialog
    ({
        autoOpen: false,
        draggable: false,
        resizable: false,
        width: 375,
        modal: true,
        buttons:{
            '': function() {
                $(this).dialog( "close");
            }
        }

    });
    if($('.ui-dialog-buttonpane').find('button').length > 0 ){
        $('.ui-dialog-buttonpane button:last').focus();
    };


    if (!fn_open_if || fn_open_if()) {
        popup.dialog('open');
        return false;
    }
    return true;
};

var openPopupWithoutButton = function(popup) {

    popup.dialog
    ({
        autoOpen: false,
        draggable: false,
        resizable: false,
        width: 375,
        modal: true
    });

    popup.dialog('open');
    return false;
};

var initPopup = function(btn, popup, fn_open_if)
{
    btn.live('click', function()
    {
        return openPopup(popup, fn_open_if);
    });
};
