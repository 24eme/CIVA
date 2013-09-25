<div class="clearfix">
	<fieldset class="clearfix">
		<legend>Vendeur</legend>

		<div class="form_col">
			<div class="bloc_condition ligne_form" data-condition-cible="#vendeur_recoltants|#vendeur_negociants|#vendeur_caves_cooperatives">
				<label for="vrac_soussignes_vendeur_type_recoltants" class="bold">Type :</label>
				<?php echo $form['vendeur_type']->render() ?>
			</div>

			<div class="nom_cvi ligne_form">
				<label for="vrac_soussignes_vendeur_recoltant_identifiant" class="bold">Nom / CVI * :</label>	
				<div id="vendeur_recoltants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY ?>">
					<?php echo $form['vendeur_recoltant_identifiant']->render() ?>
					<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY)) ?>">Ajouter à mon carnet d'adresse</a>
				</div>
				<div id="vendeur_negociants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
					<?php echo $form['vendeur_negociant_identifiant']->render() ?>
					<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY)) ?>">Ajouter à mon carnet d'adresse</a>
				</div>
				<div id="vendeur_caves_cooperatives" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
					<?php echo $form['vendeur_cave_cooperative_identifiant']->render() ?>
					<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY)) ?>">Ajouter à mon carnet d'adresse</a>
				</div>
			</div>
		</div>
		
		<?php echo $form['vendeur_recoltant_identifiant']->renderError() ?>
		<?php echo $form['vendeur_negociant_identifiant']->renderError() ?>
		<?php echo $form['vendeur_cave_cooperative_identifiant']->renderError() ?>
		<div id="vendeur_infos" class="informations form_col">
			<?php 
				if ($vrac->vendeur_identifiant) {
					include_partial('vrac/soussigne', array('tiers' => $vrac->vendeur));
				} 
			?>
		</div>
	</fieldset>

	<fieldset class="clearfix">
		<legend>Acheteur</legend>

		<div class="form_col">
			<div class="bloc_condition ligne_form" data-condition-cible="#acheteur_recoltants|#acheteur_negociants|#acheteur_caves_cooperatives">
				<label for="vrac_soussignes_acheteur_type_recoltants" class="bold">Type :</label>
				<?php echo $form['acheteur_type']->render() ?>
			</div>

			<div class="nom_cvi ligne_form">
				<label for="vrac_soussignes_acheteur_recoltant_identifiant" class="bold">Nom / CVI * :</label>	
				<div id="acheteur_recoltants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY ?>">
					<?php echo $form['acheteur_recoltant_identifiant']->render() ?>
					<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY)) ?>">Ajouter à mon carnet d'adresse</a>
				</div>

				<div id="acheteur_negociants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
					<?php echo $form['acheteur_negociant_identifiant']->render() ?>
					<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY)) ?>">Ajouter à mon carnet d'adresse</a>
				</div>
				<div id="acheteur_caves_cooperatives" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
					<?php echo $form['acheteur_cave_cooperative_identifiant']->render() ?>
					<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY)) ?>">Ajouter à mon carnet d'adresse</a>
				</div>
			</div>
		</div>

		<?php echo $form['acheteur_recoltant_identifiant']->renderError() ?>
		<?php echo $form['acheteur_negociant_identifiant']->renderError() ?>
		<?php echo $form['acheteur_cave_cooperative_identifiant']->renderError() ?>
		<div id="acheteur_infos" class="informations form_col">
			<?php 
				if ($vrac->acheteur_identifiant) {
					include_partial('vrac/soussigne', array('tiers' => $vrac->acheteur));
				} 
			?>
		</div>
	</fieldset>

	<fieldset class="clearfix">
		<legend>Courtier</legend>
		<div class="form_col">
			<p class="ligne_form">Veuillez selectionner ou saisir l'identité de l'interlocuteur commercial en charge de<br />ce contrat de vente en vrac :</p>
			<?php echo $form['interlocuteur_commercial']->render() ?>
			<a href="<?php echo url_for('@annuaire_commercial_ajouter') ?>">Ajouter à mon carnet d'adresse</a>
		</div>
		<?php echo $form['interlocuteur_commercial']->renderError() ?>
		<div id="mandataire_infos" class="informations form_col">
			<?php 
				if ($vrac->mandataire_identifiant) {
					include_partial('vrac/soussigne', array('tiers' => $vrac->mandataire));
				} 
			?>
		</div>
	</fieldset>

	<style type="text/css">
		.bloc_conditionner {
			display: none;
		}
	</style>

	<script type="text/javascript">
	$(document).ready(function () {
		$("#<?php echo $form['vendeur_recoltant_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['vendeur_negociant_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['vendeur_cave_cooperative_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['acheteur_recoltant_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['acheteur_negociant_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['acheteur_cave_cooperative_identifiant']->renderId() ?>").combobox();	
		$("#<?php echo $form['interlocuteur_commercial']->renderId() ?>").combobox();		
		$("#<?php echo $form['vendeur_recoltant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'recoltants')) ?>";
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#vendeur_infos').empty(); $('#vendeur_infos').append(data);});
		});
		$("#<?php echo $form['vendeur_negociant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'negociants')) ?>"; 
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#vendeur_infos').empty(); $('#vendeur_infos').append(data);});
		});
		$("#<?php echo $form['vendeur_cave_cooperative_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'caves_cooperatives')) ?>"; 
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#vendeur_infos').empty(); $('#vendeur_infos').append(data);});
		});
		$("#<?php echo $form['acheteur_recoltant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'recoltants')) ?>";
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#acheteur_infos').empty(); $('#acheteur_infos').append(data);});
		});
		$("#<?php echo $form['acheteur_negociant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'negociants')) ?>"; 
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#acheteur_infos').empty(); $('#acheteur_infos').append(data);});
		});
		$("#<?php echo $form['acheteur_cave_cooperative_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'caves_cooperatives')) ?>"; 
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#acheteur_infos').empty(); $('#acheteur_infos').append(data);});
		});
		$("#<?php echo $form['interlocuteur_commercial']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('@annuaire_commercial_ajouter') ?>";
			}
		});
		$(".remove_autocomplete").click(function() {$(this).parent().siblings(".informations").empty();});
	});
	</script>
</div>
	

