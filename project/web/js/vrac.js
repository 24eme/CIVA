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
	        	callback(bloc);
	        }
	        return false;
	    });
	};
	
	var initCollectionDeleteTemplate = function()
	{
		$('.btn_supprimer_ligne_template').live('click',function()
	    {
	    	var element = $(this).attr('data-container');
	        $(this).parents(element).remove();
	
	        return false;
	    });
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
                $('.popup-loading').append('<p>Le PDF de votre déclaration de stock à bien été généré, vous pouvez maintenant le télécharger.<br /><br/><a href="'+data+'" class="telecharger-ds" title="Télécharger la DS"></a></p>');
                openPopup($("#popup_loader"));

            }
        });
        return false;
    });
};
	
    var callbackAddTemplate = function(bloc) 
    {

    }

	$(document).ready(function()
	{
		 $(this).initBlocCondition();
		 initCollectionAddTemplate('.btn_ajouter_ligne_template', /var---nbItem---/g, callbackAddTemplate);
		 initCollectionDeleteTemplate();
                 initValidContratPopup();
	});
})(jQuery);