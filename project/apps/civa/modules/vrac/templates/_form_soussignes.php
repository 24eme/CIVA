<div class="clearfix">
	<fieldset class="bloc_infos">
		<legend class="titre_section">Vendeur</legend>
		
		<div class="clearfix">
			<div class="form_col selecteur">
				<div class="bloc_condition ligne_form" data-condition-cible="#vendeur_recoltants|#vendeur_negociants|#vendeur_caves_cooperatives">
					<label for="vrac_soussignes_vendeur_type_recoltants" class="bold">Type :</label>
					<?php echo $form['vendeur_type']->render() ?>
				</div>

				<div class="nom_cvi ligne_form">
					<table>
						<tr>
							<td valign="top" class="td_label"><label for="vrac_soussignes_vendeur_recoltant_identifiant" class="bold">Nom / CVI * :</label></td>
							<td valign="top">
								<div id="vendeur_recoltants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY ?>">
									<?php echo $form['vendeur_recoltant_identifiant']->render() ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY, 'acteur' => 'vendeur')) ?>">Ajouter un contact</a>
								</div>
								<div id="vendeur_negociants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
									<?php echo $form['vendeur_negociant_identifiant']->render() ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY, 'acteur' => 'vendeur')) ?>">Ajouter un contact</a>
								</div>
								<div id="vendeur_caves_cooperatives" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
									<?php echo $form['vendeur_cave_cooperative_identifiant']->render() ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY, 'acteur' => 'vendeur')) ?>">Ajouter un contact</a>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div id="vendeur_infos" class="cible">
			<?php if ($vrac->vendeur_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('tiers' => $vrac->vendeur)); ?>
			<?php endif; ?>
			</div>
		</div>

		<?php echo $form['vendeur_recoltant_identifiant']->renderError() ?>
		<?php echo $form['vendeur_negociant_identifiant']->renderError() ?>
		<?php echo $form['vendeur_cave_cooperative_identifiant']->renderError() ?>
	</fieldset>

	<fieldset class="bloc_infos">
		<legend class="titre_section">Acheteur</legend>
		
		<div class="clearfix">
			<div class="form_col selecteur">
				<div class="bloc_condition ligne_form" data-condition-cible="#acheteur_recoltants|#acheteur_negociants|#acheteur_caves_cooperatives">
					<label for="vrac_soussignes_acheteur_type_recoltants" class="bold">Type :</label>
					<?php echo $form['acheteur_type']->render() ?>
				</div>

				<div class="nom_cvi ligne_form">
					<table>
						<tr>
							<td valign="top" class="td_label"><label for="vrac_soussignes_acheteur_recoltant_identifiant" class="bold">Nom / CVI * :</label></td>
							<td valign="top">
								<div id="acheteur_recoltants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY ?>">
									<?php echo $form['acheteur_recoltant_identifiant']->render() ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY, 'acteur' => 'acheteur')) ?>">Ajouter un contact</a>
								</div>
			
								<div id="acheteur_negociants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
									<?php echo $form['acheteur_negociant_identifiant']->render() ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY, 'acteur' => 'acheteur')) ?>">Ajouter un contact</a>
								</div>
								<div id="acheteur_caves_cooperatives" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
									<?php echo $form['acheteur_cave_cooperative_identifiant']->render() ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY, 'acteur' => 'acheteur')) ?>">Ajouter un contact</a>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div id="acheteur_infos" class="cible">
			<?php if($vrac->acheteur_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('tiers' => $vrac->acheteur)); ?>
			<?php endif; ?>
			</div>
		</div>

		<?php echo $form['acheteur_recoltant_identifiant']->renderError() ?>
		<?php echo $form['acheteur_negociant_identifiant']->renderError() ?>
		<?php echo $form['acheteur_cave_cooperative_identifiant']->renderError() ?>
	</fieldset>

	<fieldset class="bloc_infos">
		<legend class="titre_section">Courtier</legend>

		<div class="clearfix">
			<div class="form_col">
				<p class="ligne_form">Veuillez selectionner l'identité de l'interlocuteur commercial en charge de ce contrat de vente en vrac :</p>
				<div class="nom_cvi ligne_form">
				<?php echo $form['interlocuteur_commercial']->render() ?>
				<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire_commercial', array('sf_subject' => $vrac)) ?>">Ajouter un contact</a>
				</div>
			</div>
			
			<div id="mandataire_infos">
			<?php if($vrac->mandataire_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('tiers' => $vrac->mandataire)); ?>
			<?php endif; ?>
			</div>
		</div>

		<?php echo $form['interlocuteur_commercial']->renderError() ?>
	</fieldset>

	<style type="text/css">
		.bloc_conditionner {
			display: none;
		}
	</style>

	<script type="text/javascript">
	$(document).ready(function () {
		$(".ajouter_annuaire").click(function() {
			$("#principal").attr('action', $(this).attr('href'));
			$("#principal").submit();
			return false;
		});
		$("#<?php echo $form['vendeur_recoltant_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['vendeur_negociant_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['vendeur_cave_cooperative_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['acheteur_recoltant_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['acheteur_negociant_identifiant']->renderId() ?>").combobox();
		$("#<?php echo $form['acheteur_cave_cooperative_identifiant']->renderId() ?>").combobox();	
		$("#<?php echo $form['interlocuteur_commercial']->renderId() ?>").combobox();		
		$("#<?php echo $form['vendeur_recoltant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				$("#principal").attr('action', '<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'recoltants', 'acteur' => 'vendeur')) ?>');
				$("#principal").submit();
				return;
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#vendeur_infos').empty(); $('#vendeur_infos').append(data);});
		});
		$("#<?php echo $form['vendeur_negociant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				$("#principal").attr('action', '<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'negociants', 'acteur' => 'vendeur')) ?>');
				$("#principal").submit();
				return;
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#vendeur_infos').empty(); $('#vendeur_infos').append(data);});
		});
		$("#<?php echo $form['vendeur_cave_cooperative_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				$("#principal").attr('action', '<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'caves_cooperatives', 'acteur' => 'vendeur')) ?>');
				$("#principal").submit();
				return;
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#vendeur_infos').empty(); $('#vendeur_infos').append(data);});
		});
		$("#<?php echo $form['acheteur_recoltant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				$("#principal").attr('action', '<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'recoltants', 'acteur' => 'acheteur')) ?>');
				$("#principal").submit();
				return;
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#acheteur_infos').empty(); $('#acheteur_infos').append(data);});
		});
		$("#<?php echo $form['acheteur_negociant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				$("#principal").attr('action', '<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'negociants', 'acteur' => 'acheteur')) ?>');
				$("#principal").submit();
				return; 
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#acheteur_infos').empty(); $('#acheteur_infos').append(data);});
		});
		$("#<?php echo $form['acheteur_cave_cooperative_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				$("#principal").attr('action', '<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => 'caves_cooperatives', 'acteur' => 'acheteur')) ?>');
				$("#principal").submit();
				return;
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#acheteur_infos').empty(); $('#acheteur_infos').append(data);});
		});
		$("#<?php echo $form['interlocuteur_commercial']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') {
				$("#principal").attr('action', '<?php echo url_for('vrac_annuaire_commercial', array('sf_subject' => $vrac)) ?>');
				$("#principal").submit();
				return; 
			}
		});
		$(".remove_autocomplete").click(function() {
			$(this).parents(".selecteur").siblings(".cible").empty();
		});
	});
	</script>
</div>
	

