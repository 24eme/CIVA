/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($)
{
	$.fn.initBlocCondition = function()
	{
		$(this).find('.bloc_condition').each(function() {
			checkUncheckCondition($(this));
		});
	}

	var checkUncheckCondition = function(blocCondition)
    {
    	var input = blocCondition.find('input');
    	var blocs = blocCondition.attr('data-condition-cible').split('|');
    	var traitement = function(input, blocs) {
		if(input.is(':checked'))
            {
        	   for (bloc in blocs) {
        		   if ($(blocs[bloc]).length>0) {
            		   var values = $(blocs[bloc]).attr('data-condition-value').split('|');
            		   for(key in values) {
            			   if (values[key] == input.val()) {
            				   $(blocs[bloc]).show();
            			   }
            		   }
        		   }
        	   }
            }
    	}
    	if(input.length == 0) {
     	   for (bloc in blocs) {
  				$(blocs[bloc]).show();
     	   }
    	} else {
     	   for (bloc in blocs) {
  				$(blocs[bloc]).hide();
     	   }
    	}
    	input.each(function() {
    		traitement($(this), blocs);
    	});

        input.click(function()
        {
      	   for (bloc in blocs) {
 				$(blocs[bloc]).hide();
    	   }
      	   if($(this).is(':checkbox')) {
          	   $(this).parent().find('input').each(function() {
	        	   traitement($(this), blocs);
          	   });
      	   } else {
      		   traitement($(this), blocs);
      	   }
        });
	}

	var initCollectionAddTemplate = function(element, regexp_replace, callback)
	{
			if($(element).length){
	    $(element).live('click', function()
	    {
	    	var lien = $(this);
	        var ligneParent = lien.parents('tr');
	    	var bloc_html = $(lien.attr('data-template')).html().replace(regexp_replace, UUID.generate());
	    	var selecteurLigne = lien.attr('data-container-last-brother');
	    	var bloc;

	        try {
				var params = jQuery.parseJSON(lien.attr('data-template-params'));
			} catch (err) {

	        }

			for(key in params) {
				bloc_html = bloc_html.replace(new RegExp(key, "g"), params[key]);
			}

			if (ligneParent.hasClass("alt")) {
				var b = $(bloc_html).addClass("alt");
				var c = b.wrap("<tbody></tbody>");
				var bloc_html = b.parent().html();
	    	}
			if (selecteurLigne) {

				if(ligneParent.find('~ '+selecteurLigne).first().size() > 0)
				{
					bloc = ligneParent.find('~ '+selecteurLigne).first().before(bloc_html);
				}
				else
				{
					bloc = ligneParent.parent().append(bloc_html);
				}

			} else {
				bloc = $($(this).attr('data-container')).append(bloc_html);
			}

	        if(callback) {
	        	callback(ligneParent, bloc_html);
	        }
	        return false;
	    });
		}
	};

	var initCollectionDeleteTemplate = function()
	{
		var btn_supprimer_ligne_template = $('.btn_supprimer_ligne_template');
		if(btn_supprimer_ligne_template.length){
			btn_supprimer_ligne_template.live('click',function()
			{
				var element = $(this).attr('data-container');
				$(this).parents(element).remove();
				var brothers = $('.'+$(this).attr('data-brother'));
				var cible = $('#'+$(this).attr('data-mother'));
				sumContrat(brothers, cible);

				return false;
			});
		}
	}



        /**
 * Initalise la popup previsualisation des Contrat
 ******************************************/
var initValidContratPopup = function()
{
    $('#previsualiserContrat').click(function() {
        openPopup($("#popup_loader"));
        $.ajax({
            url: ajax_url_to_print,
            success: function(data) {
                $('.popup-loading').empty();
                $('.popup-loading').css('background', 'none');
                $('.popup-loading').css('padding-top', '10px');
                $('.popup-loading').append('<p>Le PDF de votre contrat en vrac à bien été généré, vous pouvez maintenant le télécharger.<br /><br/><a href="'+data+'" class="telecharger-contrat" title="Télécharger le contrat"></a></p>');
                openPopup($("#popup_loader"));

            }
        });
        return false;
    });
};

var initConfirmeValidationVrac = function()
{
    $('#valideVrac').click(function() {
        openPopup($("#popup_confirme_validationVrac"));
		$("#popup_confirme_validationVrac #valideVrac_OK").focus();

		return false;
    });
    $('#valideVrac_OK').click(function() {
        $("#popup_confirme_validationVrac").dialog('close');
        $("#principal").submit();
        return false;
    });
};

var initConfirmeSignatureVrac = function()
{
    $('#signatureVrac').click(function() {
        openPopup($("#popup_confirme_signatureVrac"));
		$("#popup_confirme_signatureVrac #signatureVrac_OK").focus();
        return false;
    });
    $('#signatureVrac_OK').click(function() {
        $("#popup_confirme_signatureVrac").dialog('close');
        document.location.href=$('#signatureVrac').attr("href");
        return false;
    });
};

var initChoixTypeVrac = function()
{
    $('.choixTypeVracPopup').click(function() {
        openPopup($("#popup_choix_typeVrac"));
        return false;
    });

	$('.choixTypeVracPopupPapier').click(function() {
        openPopup($("#popup_choix_typeVracPapier"));
        return false;
    });
};

var initClotureContrat = function()
{
    $('#clotureContrat_OK').click(function() {
        $("#popup_cloture_contrat").dialog('close');
        document.location.href=$('#cloture_contrat_form').attr("action");
        return false;
    });
};

var initClotureContratCheckboxes = function()
{
    $('.cloture input').change(function (){
        $(this).checkboxesBehaviour();
    });

};

$.fn.checkboxesBehaviour = function(){
	var reg_debut = new RegExp("vrac_produits__", "g");
  var reg_fin = new RegExp("(_detail_[0-9]+)_[A-Za-z0-9\_\-]*", "g");
	if($(this).attr('id') == undefined){
		return false;
	}
  var champsClass = $(this).attr('id').replace(reg_fin,'$1').replace(reg_debut,'ret_');
  var date = $('.'+champsClass).parent().parent().children('td.echeance').children('input.input_date');
    if($(this).is(':checked')){
        $('.'+champsClass).attr('readonly','readonly');
        date.attr('readonly','readonly');
        date.datepicker('destroy');

    }else{
        $('.'+champsClass).removeAttr('readonly');
        date.removeAttr('readonly');
        date.datepickerInit();
    }
};

$.fn.datepickerInit = function(){
    $(this).datepicker({
    		        changeMonth: true,
    		        changeYear: true,
    		        dateFormat: 'dd/mm/yy',
    		        dayNames: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
    		        dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
    		        firstDay: 1,
    		        monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
    		        monthNamesShort: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
    		        yearRange: '1900:'+new Date().getFullYear()
    		    });
};

var initSummableContrat = function()
{
    $('.summable').blur(function() {
    	var brothers = $('.'+$(this).attr('data-brother'));
    	var cible = $('#'+$(this).attr('data-mother'));
    	sumContrat(brothers, cible);
    });
};

var sumContrat = function(brothers, cible)
{
	var sum = 0;
	var compare = parseFloat($('#'+cible.attr('data-compare')).text());
	var cb = $('#'+cible.attr('data-cibling'));
	if (isNaN(compare)) {
		compare = 0;
	} else {
		compare = compare - compare * 0.1;
	}
	brothers.each(function() {
		var value = parseFloat($(this).val());
                if($(this).attr('readonly')=='readonly') return;
                var reg = new RegExp("enlevements([A-Za-z0-9\_\-])*", "g");
                var idCheckbox = $(this).attr('id').replace(reg,'cloture');
		if (!isNaN(value)) {
    		sum = sum + value;
                if(sum > 0) {
                    $('#'+idCheckbox).show();
                    cb.checkboxesBehaviour();
                    }
		}
                else{
                if(sum <= 0){
                    $('#'+idCheckbox).hide();
                    cb.checkboxesBehaviour();
                }
                }
	});
	sum = sum.toFixed(2);
	cible.text(sum);
	if (sum >= compare) {
		cb.attr('checked', true);
                cb.checkboxesBehaviour();

	}
};

    var callbackAddTemplate = function(ligneParent, bloc)
    {
    	initNettoyageChamps();
        initSummableContrat();
    	$('.datepicker').datepickerInit();
    }

    /**
	*  Valide les champs du tableaux de
	*  l'étape produits de la création de contrats
	******************************************/
	$.initChampsTableauProduits = function(params)
	{
		var contexte = $('#contrats_vrac'),
			tableau = contexte.find('.produits.table_donnees'),
			lignes_tableau = tableau.find('tr'),
			derniere_ligne = lignes_tableau.last(),
			btnValidation = tableau.parent().next().find('.suiv button'),
			ligne_valide = false;

		// On parcourt chaque ligne
		lignes_tableau.each(function()
		{
			var ligne_courante = $(this),
				champs = ligne_courante.find('input, select'),
				btn_balayette = ligne_courante.find('a.balayette');

			// Vérifie les champs requis
			var verifChampsRequis = function()
			{
				var champs_vides = true;

				champs.each(function()
				{
					$(this).removeClass("champ_rouge");

					var champ_volume = ligne_courante.find('.volume input'),
						champ_prix = ligne_courante.find('.prix input'),
						champ_centilisation = ligne_courante.find('.centilisation select'),
						champ_bouteille = ligne_courante.find('.bouteille input');
						champ_bio = ligne_courante.find('.bio select');
					var class_red = [];
				  var empty = true;
					champs.each(function(){
						if($.trim($(this).val())){ empty = false; }
					});
					if(champ_bio.length > 0 && !$.trim(champ_bio.val())){
						class_red.push(champ_bio);
					}
					if(champ_volume.length > 0 && !$.trim(champ_volume.val())){
						class_red.push(champ_volume);
					}
					if(champ_prix.length > 0 && !$.trim(champ_prix.val())){
						class_red.push(champ_prix);
					}
					if(champ_centilisation.length > 0 && !champ_centilisation.val()){
						class_red.push(champ_centilisation);
					}
					if(champ_bouteille.length > 0 && !$.trim(champ_bouteille.val())){
						class_red.push(champ_bouteille);
					}
					if(!class_red.length)
					{
						ligne_courante.addClass('coche');
						ligne_courante.removeClass('croix');
						ligne_valide = true;
					}
					else
					{
						ligne_courante.removeClass('coche');
						ligne_courante.addClass('croix');
						if(!empty){
							for (var i = 0; i < class_red.length; i++) {
								class_red[i].addClass("champ_rouge");
							}
						}
					}

					if($.trim($(this).val()))
					{
						champs_vides = false;
						ligne_courante.addClass('actif');
					}
				});

				if(champs_vides)
				{
					ligne_courante.removeClass('actif');
					ligne_courante.removeClass('croix');
				}
			};

			verifChampsRequis();

			// Ligne active
			champs.focus(function()
			{
				ligne_courante.addClass('actif');
			});

			// Affichage du picto coche
			champs.blur(function()
			{
				var ligne_valide = false;

				verifChampsRequis();

				// On vérifie si le tableau comporte une ligne valide
				lignes_tableau.each(function()
				{
					if($(this).hasClass('coche'))
					{
						ligne_valide = true;
					}
				});

				// Si le tableau n'a pas de lignes valides on désactive le btn continuer
				if(!ligne_valide)
				{
					btnValidation.addClass('btn_inactif').attr('disabled', 'disabled');
				}else
				{
					btnValidation.removeClass('btn_inactif').removeAttr('disabled');
				}
			});

			// Affichage du picto balayette
			ligne_courante.hover
			(
				function()
				{
					champs.each(function()
					{
						if($.trim($(this).val()) !== '')
						{
							ligne_courante.addClass('effacable');
						}
					});
				},

				function()
				{
					$(this).removeClass('effacable');
				}
			);

			btn_balayette.click(function()
			{
				champs.val('');
				ligne_courante.removeClass('coche effacable croix actif');

				return false;
			});
		});

		if(!ligne_valide)
		{
			btnValidation.addClass('btn_inactif').attr('disabled', 'disabled');
		}

		// Si on vient de la page ajout de produit
		if(params.ajoutProduit)
		{
			// On met le focus sur la dernière ligne
			derniere_ligne.find('input:first').focus();
		}
	};

	/**
	*  Fixe la hauteur des listes de l'annuaire
	*  par rapport à un nombre de lignes donné
	******************************************/
	$.hauteurListesAnnuaire = function()
	{
		var liste = $('#contrats_vrac .bloc_annuaire .bloc'),
			ligne = liste.find('ul li'),
			hauteurLigne = ligne.outerHeight(),
			hauteurBlocFinale = hauteurLigne * 10;

		liste.height(hauteurBlocFinale);
	};

	$(document).ready(function()
	{
		 $(this).initBlocCondition();
		 initCollectionAddTemplate('.btn_ajouter_ligne_template', /var---nbItem---/g, callbackAddTemplate);
		 initCollectionDeleteTemplate();
                 initValidContratPopup();

         $.hauteurListesAnnuaire();
         initConfirmeSignatureVrac();
         initChoixTypeVrac();
         initConfirmeValidationVrac();
         initClotureContrat();
         initSummableContrat();
         initClotureContratCheckboxes();
	});

})(jQuery);
