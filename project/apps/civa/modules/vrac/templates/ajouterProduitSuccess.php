<div class="clearfix">
	<?php include_partial('vrac/etapes', array('vrac' => $vrac, 'etapes' => $etapes, 'current' => $etape, 'user' => $user)) ?>
</div>

<div id="contrats_vrac">

	<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_ajout_produit', array('sf_subject' => $vrac, 'etape' => $etape)) ?>">
		<?php echo $form->renderHiddenFields() ?>

		<div class="fond">

			<span id="errors"><?php echo $form['hash']->renderError() ?></span>
			<h2 class="titre_section">Ajouter un produit</h2>

			<table class="ajout_produit table_donnees">
				<tr id="ligne_appellation">
					<th>Appellation :</th>
					<td>
						<select id="choix_appellation" name="appellation">
							<option value="">--</option>
							<?php foreach ($form->getAppellations() as $key => $appellation): ?>
							<option value="<?php echo $key ?>"><?php echo $appellation->libelle ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr id="ligne_lieu_dit" class="hidden">
					<th>Lieu-Dit* :</th>
					<td>
						<?php echo $form['lieu_dit']->render() ?>
						<span><?php echo $form['lieu_dit']->renderError() ?></span>
					</td>
				</tr>
				<tr id="ligne_lieu" class="hidden">
					<th>Lieu :</th>
					<td>
						<select id="choix_lieu" name="lieu"></select>
					</td>
				</tr>
				<tr id="ligne_cepage" class="hidden">
					<th>Cépage :</th>
					<td>
						<select id="choix_cepage" name="cepage"></select>
					</td>
				</tr>
				<tr id="ligne_vtsgn" class="hidden">
					<th>VT/SGN :</th>
					<td>
						<?php echo $form['vtsgn']->render() ?>
						<span><?php echo $form['vtsgn']->renderError() ?></span>
					</td>
				</tr>
			</table>
		</div>
		<ul class="btn_prev_suiv clearfix" id="btn_etape">
		    <li class="prec">
	            <a id="btn_precedent" href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'etape' => $etape)) ?>">
	                <img alt="Retourner à l'étape précédente" src="/images/boutons/btn_retour.png">
	            </a>
		    </li>
		    <li class="suiv">
		    	<button id="valide_form" type="submit" name="valider" class="btn_image" style="cursor: pointer;">
		    		<img alt="Continuer à l'étape suivante" src="/images/boutons/btn_valider_2.png" />
		    	</button>
		    </li>
		</ul>
	</form>
</div>
<script type="text/javascript">
$(document).ready(function(){

	$("#valide_form").click(function() {
		var valide = true;
		var errors = "<ul class=\"error_list\">";
		if (!$("#vrac_produit_ajout_hash").val()) {
			valide = false;
			errors += "<li>La saisie du produit n'est pas complète.</li>";
		}
		if (!$("#ligne_lieu_dit").hasClass('hidden') && !$("#vrac_produit_ajout_lieu_dit").val()) {
			valide = false;
			errors += "<li>Vous devez spécifier le lieu-dit.</li>";
		}
		errors += "</ul>";
		if (valide) {
			$("#principal").submit();
		} else {
			$("#errors").html(errors);
		}
		return false;
	});

	var appellationsLieuDit = Object.keys(jQuery.parseJSON('<?php echo $sf_data->getRaw('appellationsLieuDit'); ?>'));
    $("#choix_appellation").change(function() {
    	$("#errors").html('');
        var value = $(this).val();

        $.post("<?php echo url_for('vrac_ajout_produit_lieux', array('sf_subject' => $vrac, 'etape' => $etape)) ?>", { appellation: value }, function(data){
        	$("#<?php echo $form['hash']->renderId() ?>").val(null);
			var json = jQuery.parseJSON(data);
			var size = Object.keys(json).length;
			if (size > 0) {
				var opts = '<option value="">--</option>';
	        	for(var k in json) {
	        		opts += '<option value="'+k+'">'+json[k]+'</option>';
	        	}
	        	$("#choix_lieu").html(opts);
	        	$("#ligne_lieu").removeClass("hidden");
	        	$("#ligne_cepage").addClass("hidden");
			} else {
				$("#ligne_lieu").addClass("hidden");
				$("#choix_lieu").trigger("change");
			}
        });
        if (jQuery.inArray(value, appellationsLieuDit) != -1) {
        	$("#ligne_lieu_dit").removeClass("hidden");
        } else {
        	$("#ligne_lieu_dit").addClass("hidden");
        	$("#choix_lieu").html('<option value="">--</option>');
        }
    });

    $("#choix_lieu").change(function() {
    	$("#errors").html('');
        var appellation = $("#choix_appellation").val();
        var value = $(this).val();
        if (!value) {
			value = '';
        }
        $.post("<?php echo url_for('vrac_ajout_produit_cepages', array('sf_subject' => $vrac, 'etape' => $etape)) ?>", { appellation: appellation, lieu: value }, function(data){
        	$("#<?php echo $form['hash']->renderId() ?>").val(null);
			var json = jQuery.parseJSON(data);
			var size = Object.keys(json).length;
			if (size > 0) {
				var opts = '<option value="">--</option>';
	        	for(var k in json) {
	        		opts += '<option value="'+k+'">'+json[k]+'</option>';
	        	}
	        	$("#choix_cepage").html(opts);
	        	$("#ligne_cepage").removeClass("hidden");
			}
        });
    });

    $("#choix_cepage").change(function() {
    	$("#errors").html('');
        var value = $(this).val();
        if (!value) {
			value = '';
        }
		$("#<?php echo $form['hash']->renderId() ?>").val(value);
		$.post("<?php echo url_for('vrac_ajout_produit_vtsgn', array('sf_subject' => $vrac, 'etape' => $etape)) ?>", { hash: value }, function(data){
				if (data == 0) {
					$("#ligne_vtsgn").addClass("hidden");
				} else {
					$("#ligne_vtsgn").removeClass("hidden");
				}
	    });
    });
});
</script>
<style type="text/css">
.hidden {
	display: none;
}
</style>
