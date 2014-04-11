
<div class="clearfix">
	<fieldset class="bloc_infos">
		<legend class="titre_section">Type de contrat</legend>
		
		<div class="clearfix">
			<div class="form_col selecteur">
				<div class="ligne_form">
					<label for="vrac_soussignes_vendeur_type_recoltants" class="bold">Veuillez selectionner le type de votre contrat :</label>
					<?php echo ($form->getObject()->isNew())? $form['type_contrat']->render() : $form['type_contrat']->render(array('readonly' => 'readonly')); ?>
				</div>
			</div>
		</div>
	</fieldset>
	
	<p class="intro_contrat_vrac">Saisissez ici les noms ou CVI des soussignés concernés par le contrat. Si ceux-ci ne sont pas déjà listés dans l'annuaire de vos interlocuteurs, vous pouvez ajouter un contact à partir de son CVI.</p>
	
	<fieldset class="bloc_infos">
		<legend class="titre_section">Vendeur</legend>
		
		<div class="clearfix">
			<div class="form_col selecteur">
				<div class="bloc_condition ligne_form" data-condition-cible="#vendeur_recoltants|#vendeur_negociants|#vendeur_caves_cooperatives">
					<label for="vrac_soussignes_vendeur_type_recoltants" class="bold">Type :</label>
					<?php echo $form['vendeur_type']->render() ?>
				</div>

				<div class="nom_cvi ligne_form">

					<?php echo $form['vendeur_recoltant_identifiant']->renderError() ?>
					<?php echo $form['vendeur_negociant_identifiant']->renderError() ?>
					<?php echo $form['vendeur_cave_cooperative_identifiant']->renderError() ?>
					<table>
						<tr>
							<td valign="top" class="td_label"><label for="vrac_soussignes_vendeur_recoltant_identifiant" class="bold">Nom / CVI * :</label></td>
							<td valign="top">
								<div id="vendeur_recoltants" data-acteur="vendeur" data-type="recoltant" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY ?>">
									<?php echo $form['vendeur_recoltant_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "vendeur", "data-type" => "recoltants")) ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY, 'acteur' => 'vendeur')) ?>">Ajouter un contact</a>
								</div>
								<div id="vendeur_negociants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
									<?php echo $form['vendeur_negociant_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "vendeur", "data-type" => "negociants")) ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY, 'acteur' => 'vendeur')) ?>">Ajouter un contact</a>
								</div>
								<div id="vendeur_caves_cooperatives" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
									<?php echo $form['vendeur_cave_cooperative_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "vendeur", "data-type" => "caves_cooperatives")) ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY, 'acteur' => 'vendeur')) ?>">Ajouter un contact</a>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>

			<div id="vendeur_infos" class="cible">
			<?php if ($vrac->vendeur_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->vendeur, 'fiche' => false)); ?>
			<?php endif; ?>
			</div>
		</div>
	</fieldset>
	
	<?php if (!$vrac->isAcheteurProprietaire()): ?>
	<fieldset class="bloc_infos">
		<legend class="titre_section">Acheteur</legend>
		
		<div class="clearfix">
			<div class="form_col selecteur">
				<div class="bloc_condition ligne_form" data-condition-cible="#acheteur_recoltants|#acheteur_negociants|#acheteur_caves_cooperatives">
					<label for="vrac_soussignes_acheteur_type_recoltants" class="bold">Type :</label>
					<?php echo $form['acheteur_type']->render() ?>
				</div>
		
				<div class="nom_cvi ligne_form">

					<?php echo $form['acheteur_recoltant_identifiant']->renderError() ?>
					<?php echo $form['acheteur_negociant_identifiant']->renderError() ?>
					<?php echo $form['acheteur_cave_cooperative_identifiant']->renderError() ?>
					<table>
						<tr>
							<td valign="top" class="td_label"><label for="vrac_soussignes_acheteur_recoltant_identifiant" class="bold">Nom / CVI * :</label></td>
							<td valign="top">
								<div id="acheteur_recoltants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY ?>">
									<?php echo $form['acheteur_recoltant_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "acheteur", "data-type" => "recoltants")) ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY, 'acteur' => 'acheteur')) ?>">Ajouter un contact</a>
								</div>
			
								<div id="acheteur_negociants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
									<?php echo $form['acheteur_negociant_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "acheteur", "data-type" => "negociants")) ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY, 'acteur' => 'acheteur')) ?>">Ajouter un contact</a>
								</div>
								<div id="acheteur_caves_cooperatives" data-acteur="cave_cooperative" data-type="recoltant" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
									<?php echo $form['acheteur_cave_cooperative_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "acheteur", "data-type" => "caves_cooperatives")) ?>
									<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY, 'acteur' => 'acheteur')) ?>">Ajouter un contact</a>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div id="acheteur_infos" class="cible">
			<?php if($vrac->acheteur_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->acheteur, 'fiche' => false)); ?>
			<?php endif; ?>
			</div>
		</div>
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
				<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->mandataire, 'fiche' => false)); ?>
			<?php endif; ?>
			</div>
		</div>

		<?php echo $form['interlocuteur_commercial']->renderError() ?>
	</fieldset>
	<?php else: ?>
	
	<fieldset class="bloc_infos">
		<legend class="titre_section">Acheteur</legend>

		<div class="clearfix">
			<div class="form_col">
				<p class="ligne_form">Veuillez selectionner l'identité de l'interlocuteur commercial en charge de ce contrat de vente en vrac :</p>
				<div class="nom_cvi ligne_form">
				<?php echo $form['interlocuteur_commercial']->render() ?>
				<a class="ajouter_annuaire" href="<?php echo url_for('vrac_annuaire_commercial', array('sf_subject' => $vrac)) ?>">Ajouter un contact</a>
				</div>
			</div>
			
			
			<div id="acheteur_infos" class="cible">
			<?php if($vrac->acheteur_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->acheteur, 'fiche' => false)); ?>
			<?php endif; ?>
			</div>
		</div>

		<?php echo $form['interlocuteur_commercial']->renderError() ?>
	</fieldset>
	<?php endif; ?>
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

		var url_annuaire = "<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => '-type-', 'acteur' => '-acteur-')) ?>";
		var url_soussigne = "<?php echo url_for('vrac_soussigne_informations', array('sf_subject' => $vrac, 'acteur' => '-acteur-')) ?>";

		$("select.choix_soussigne").combobox();
		$("#<?php echo $form['interlocuteur_commercial']->renderId() ?>").combobox();
		$("select.choix_soussigne").change(function() { 
			var acteur = $(this).attr('data-acteur');
			var type = $(this).attr('data-type');
			if ($(this).val() == 'add') { 
				$("#principal").attr('action', url_annuaire.replace('-type-', type).replace('-acteur-', acteur));
				$("#principal").submit();
				return;
			}
			$.post(url_soussigne.replace('-acteur-', acteur), { identifiant: $(this).val() }, function(data) {$('#'+acteur+'_infos').empty(); $('#'+acteur+'_infos').append(data);});
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
	

