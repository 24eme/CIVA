
<div class="clearfix">
	<fieldset id="type_contrat" class="bloc_infos" style="margin-bottom: 25px;">
		<legend class="titre_section">Type de contrat</legend>

		<div class="clearfix">
			<div id="type_contrat_radio_list" class="form_col form_col_extended selecteur">
				<div class="ligne_form<?php if (!$form->getObject()->isNew()): ?>_sm<?php endif; ?>">
					<label for="vrac_soussignes_vendeur_type_recoltants" class="bold"><?php if ($form->getObject()->isNew()): ?>Type du contrat :<?php else: ?>Type du contrat : <?php endif; ?></label>
					<?php if ($form->getObject()->isNew()): ?>
						<?php echo $form['type_contrat']->render(array('autofocus' => 'autofocus')); ?>
					<?php else: ?>
						<ul class="radio_list"><li><label for="vrac_soussignes_type_contrat_<?php echo $form->getObject()->type_contrat ?>"><?php echo ucfirst(strtolower($form->getObject()->type_contrat)); ?>&nbsp;<?php echo ($form->getObject()->contrat_pluriannuel)? 'pluriannuel' : 'ponctuel'; ?></label></li></ul>
					<?php endif; ?>
				</div>
			</div>
            <?php if ($form->getObject()->isNew()): ?>
            <div id="contrat_pluriannuel_radio_list" class="form_col form_col_extended selecteur">
                <div class="ligne_form">
					<label for="" class="bold">Durée du contrat :</label>
                    <?php echo $form['contrat_pluriannuel']->render(); ?>
                </div>
            </div>
            <?php endif; ?>
			<?php if ($form->getObject()->isNew()): ?>
            <div id="contrat_pluriannuel_inputs" style="display: none;">
            <div class="form_col form_col_extended selecteur" style="padding-top: 0;">
                <div id="ligne_campagnes_application" class="ligne_form">
                    <?php echo $form['campagne']->renderError() ?>
    				<?php echo $form['campagne']->renderLabel(null, array("class" => "bold", "style" => "opacity: 0.25;")) ?>
    				<?php echo $form['campagne']->render(array("disabled" => "disabled", "style" => "margin-left: 5px; width: 120px;")) ?>
                </div>
            </div>
            <?php if(isset($form['contrat_pluriannuel_mode_surface'])): ?>
            <div class="form_col form_col_extended selecteur" style="padding-top: 0;">
                <div id="ligne_contrat_pluriannuel_mode_surface" class="ligne_form">
                    <?php echo $form['contrat_pluriannuel_mode_surface']->renderError() ?>
				    <?php echo $form['contrat_pluriannuel_mode_surface']->renderLabel(null, array("class" => "bold", "style" => "opacity: 0.25;")) ?>
					<?php echo $form['contrat_pluriannuel_mode_surface']->render(array("disabled" => "disabled")) ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if(isset($form['prix_unite'])): ?>
            <div class="form_col form_col_extended selecteur" style="padding-top: 0;">
                <div id="ligne_prix_unite" class="ligne_form">
                    <?php echo $form['prix_unite']->renderError() ?>
				    <?php echo $form['prix_unite']->renderLabel(null, array("class" => "bold", "style" => "opacity: 0.25;")) ?>
					<?php echo $form['prix_unite']->render(array("disabled" => "disabled", "style" => "margin-left: 5px; width: 120px;")) ?>
                </div>
            </div>
            <?php endif; ?>
            </div>
            <script>
				document.querySelectorAll('input[name="vrac_soussignes[type_contrat]"]').forEach(function(input) {
					input.addEventListener('change', function(e) {
						document.querySelector('#vrac_soussignes_contrat_pluriannuel_mode_surface_0').checked = true;
						document.querySelector('#vrac_soussignes_prix_unite').value = "EUR_HL";
						if(document.querySelector('#vrac_soussignes_contrat_pluriannuel_1').checked) {
							document.querySelector('#vrac_soussignes_contrat_pluriannuel_mode_surface_0').disabled = (input.value == "RAISIN");
						}
						if(input.value == "RAISIN") {
							document.querySelector('#vrac_soussignes_prix_unite').value = "EUR_KG";
							document.querySelector('#vrac_soussignes_contrat_pluriannuel_mode_surface_1').checked = true;
						}
					});
				});
                document.querySelector('#vrac_soussignes_contrat_pluriannuel_0').addEventListener('change', function(e) {
                    document.getElementById('contrat_pluriannuel_inputs').style.display = 'none';
                    document.querySelector('#ligne_campagnes_application select').disabled = true;
                    document.querySelector('#ligne_prix_unite select').disabled = true;
                    document.querySelectorAll('#ligne_contrat_pluriannuel_mode_surface input').disabled = true;
                    document.querySelectorAll('#ligne_contrat_pluriannuel_mode_surface input').forEach(function(item) {
                      item.disabled = true;
                    });
                    document.querySelector('#ligne_campagnes_application label').style.opacity = '0.25';
                    document.querySelector('#ligne_prix_unite label').style.opacity = '0.25';
                    document.querySelector('#ligne_contrat_pluriannuel_mode_surface label').style.opacity = '0.25';
                });
                document.querySelector('#vrac_soussignes_contrat_pluriannuel_1').addEventListener('change', function(e) {
                    document.getElementById('contrat_pluriannuel_inputs').style.display = 'block';
                    document.querySelector('#ligne_campagnes_application select').disabled = false;
                    document.querySelector('#ligne_prix_unite select').disabled = false;
                    document.querySelectorAll('#ligne_contrat_pluriannuel_mode_surface input').forEach(function(item) {
                      item.disabled = false;
                    });
					if(document.querySelector('#vrac_soussignes_type_contrat_RAISIN').checked) {
						console.log('disabled');
						document.querySelector('#vrac_soussignes_contrat_pluriannuel_mode_surface_0').disabled = true;
					}
                    document.querySelector('#ligne_campagnes_application label').style.opacity = '1';
                    document.querySelector('#ligne_prix_unite label').style.opacity = '1';
                    document.querySelector('#ligne_contrat_pluriannuel_mode_surface label').style.opacity = '1';
                });
            </script>
            <?php elseif($vrac->isPluriannuelCadre()): ?>
            <div class="form_col form_col_extended selecteur" style="padding-top: 0;">
                <div class="ligne_form_sm">
					<label for="" class="bold">Campagnes d'application :</label>
                    <span style="margin-left: 5px;"><?php $millesime = substr($vrac->campagne, 0, 4)*1; echo $millesime; ?> à <?php echo ($millesime+VracClient::getConfigVar('nb_campagnes_pluriannuel',0)-1) ?></span>
                </div>
            </div>
            <div class="form_col form_col_extended selecteur" style="padding-top: 0;">
                <div class="ligne_form_sm">
					<label for="" class="bold">Vous contractualisez sur :</label>
                    <span style="margin-left: 5px;"><?php if(!$vrac->isInModeSurface()): ?>Du volume (hl)<?php else: ?>De la surface (ares)<?php endif; ?></span>
                </div>
            </div>
            <div class="form_col form_col_extended selecteur" style="padding-top: 0;">
                <div class="ligne_form_sm">
					<label for="" class="bold">Unité de prix :</label>
                    <span style="margin-left: 5px;"><?php echo $vrac->getPrixUniteLibelle(); ?></span>
                </div>
            </div>
            <?php endif; ?>
		</div>
	</fieldset>

	<p class="intro_contrat_vrac">Saisissez ici les noms ou CVI des soussignés concernés par le contrat. Si ceux-ci ne sont pas déjà listés dans l'annuaire de vos interlocuteurs, vous pouvez ajouter un contact à partir de son CVI.</p>

	<?php if (!$vrac->isVendeurProprietaire()): ?>
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
									<?php if(!$vrac->isPapier()): ?>
									<div class="ajouter_annuaire">
										<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY, 'acteur' => 'vendeur')) ?>">Ajouter un contact</a>
									</div>
									<?php endif; ?>
								</div>
								<div id="vendeur_negociants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
									<?php echo $form['vendeur_negociant_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "vendeur", "data-type" => "negociants")) ?>
									<?php if(!$vrac->isPapier()): ?>
									<div class="ajouter_annuaire">
										<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY, 'acteur' => 'vendeur')) ?>">Ajouter un contact</a>
									</div>
									<?php endif; ?>
								</div>
								<div id="vendeur_caves_cooperatives" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
									<?php echo $form['vendeur_cave_cooperative_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "vendeur", "data-type" => "caves_cooperatives")) ?>
									<?php if(!$vrac->isPapier()): ?>
									<div class="ajouter_annuaire">
										<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY, 'acteur' => 'vendeur')) ?>">Ajouter un contact</a>
									</div>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					</table>
				</div>
                <div class="ligne_form">
                    <label for="vrac_soussignes_acheteur_assujetti_tva" class="bold">Le vendeur est assujeti à la TVA</label>
                    <?php echo $form['vendeur_assujetti_tva']->render(array('required' => 'required')); ?>
                </div>
			</div>

			<div id="vendeur_infos" class="cible">
			<?php if ($vrac->vendeur_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->vendeur, 'fiche' => false)); ?>
			<?php endif; ?>
			</div>
		</div>
	</fieldset>
	<?php else: ?>
	<fieldset class="bloc_infos">
		<legend class="titre_section">Vendeur</legend>

		<div class="clearfix">
			<div class="form_col">
				<?php if(isset($form['interlocuteur_commercial'])): ?>
				<p class="ligne_form">Veuillez selectionner l'identité de l'interlocuteur commercial en charge de ce contrat de vente en vrac :</p>
				<div class="nom_cvi ligne_form">
				<?php echo $form['interlocuteur_commercial']->render() ?>
				<div class="ajouter_annuaire">
					<a href="<?php echo url_for('vrac_annuaire_commercial', array('sf_subject' => $vrac)) ?>">Ajouter un contact</a>
				</div>
				</div>
				<?php else: ?>
					<p class="ligne_form">Ce soussigné est à l'origine du contrat.</p>
				<?php endif; ?>
                <div class="ligne_form">
                    <label for="vrac_soussignes_acheteur_assujetti_tva" class="bold">Le vendeur est assujeti à la TVA</label>
                    <?php echo $form['vendeur_assujetti_tva']->render(array('required' => 'required')); ?>
                </div>
			</div>


			<div id="vendeur_infos" class="cible">
			<?php if($vrac->vendeur_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->vendeur, 'fiche' => false)); ?>
			<?php endif; ?>
			</div>
		</div>
		<?php if(isset($form['interlocuteur_commercial'])): ?>
			<?php echo $form['interlocuteur_commercial']->renderError() ?>
		<?php endif; ?>
	</fieldset>
	<?php endif; ?>

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
									<?php echo $form['acheteur_recoltant_identifiant']->render(array("class" => "choix_soussigne select2autocomplete", "data-acteur" => "acheteur", "data-type" => "recoltants")) ?>
									<?php if(!$vrac->isPapier()): ?>
									<div class="ajouter_annuaire">
										<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY, 'acteur' => 'acheteur')) ?>">Ajouter un contact</a>
									</div>
									<?php endif; ?>
								</div>

								<div id="acheteur_negociants" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
									<?php echo $form['acheteur_negociant_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "acheteur", "data-type" => "negociants")) ?>
									<?php if(!$vrac->isPapier()): ?>
									<div class="ajouter_annuaire">
										<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY, 'acteur' => 'acheteur')) ?>">Ajouter un contact</a>
									</div>
									<?php endif; ?>
								</div>
								<div id="acheteur_caves_cooperatives" data-acteur="cave_cooperative" data-type="recoltant" class="bloc_conditionner ligne_form" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
									<?php echo $form['acheteur_cave_cooperative_identifiant']->render(array("class" => "choix_soussigne", "data-acteur" => "acheteur", "data-type" => "caves_cooperatives")) ?>
									<?php if(!$vrac->isPapier()): ?>
									<div class="ajouter_annuaire">
										<a href="<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY, 'acteur' => 'acheteur')) ?>">Ajouter un contact</a>
									</div>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					</table>
				</div>
                <div class="ligne_form">
                    <label for="vrac_soussignes_acheteur_assujetti_tva" class="bold">L'acheteur est assujeti à la TVA</label>
                    <?php echo $form['acheteur_assujetti_tva']->render(array('required' => 'required')); ?>
                </div>
			</div>
			<div id="acheteur_infos" class="cible">
			<?php if($vrac->acheteur_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->acheteur, 'fiche' => false)); ?>
			<?php endif; ?>
			</div>
		</div>
	</fieldset>
	<?php if (!$vrac->isVendeurProprietaire()): ?>
	<fieldset class="bloc_infos">
		<legend class="titre_section">Courtier</legend>

		<div class="clearfix">
			<div class="form_col">
				<?php if(isset($form['interlocuteur_commercial'])): ?>
				<p class="ligne_form">Veuillez selectionner l'identité de l'interlocuteur commercial en charge de ce contrat de vente en vrac :</p>
				<div class="nom_cvi ligne_form">
				<?php echo $form['interlocuteur_commercial']->render() ?>
				<div class="ajouter_annuaire">
					<a href="<?php echo url_for('vrac_annuaire_commercial', array('sf_subject' => $vrac)) ?>">Ajouter un contact</a>
				</div>
				</div>
				<?php else: ?>
					<p class="ligne_form">Ce soussigné est à l'origine du contrat.</p>
				<?php endif; ?>
			</div>

			<div id="mandataire_infos">
			<?php if($vrac->mandataire_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->mandataire, 'fiche' => false)); ?>
			<?php endif; ?>
			</div>
		</div>
		<?php if(isset($form['interlocuteur_commercial'])): ?>
			<?php echo $form['interlocuteur_commercial']->renderError() ?>
		<?php endif; ?>
	</fieldset>
	<?php endif; ?>
	<?php else: ?>

	<fieldset class="bloc_infos">
		<legend class="titre_section">Acheteur</legend>

		<div class="clearfix">
			<div class="form_col">
				<?php if(isset($form['interlocuteur_commercial'])): ?>
				<p class="ligne_form">Veuillez selectionner l'identité de l'interlocuteur commercial en charge de ce contrat de vente en vrac :</p>
				<div class="nom_cvi ligne_form">
				<?php echo $form['interlocuteur_commercial']->render() ?>
				<div class="ajouter_annuaire">
					<a href="<?php echo url_for('vrac_annuaire_commercial', array('sf_subject' => $vrac)) ?>">Ajouter un contact</a>
				</div>
				</div>
				<?php else: ?>
					<p class="ligne_form">Ce soussigné est à l'origine du contrat.</p>
				<?php endif; ?>
                <div class="ligne_form">
                    <label for="vrac_soussignes_acheteur_assujetti_tva" class="bold">L'acheteur est assujeti à la TVA</label>
                    <?php echo $form['acheteur_assujetti_tva']->render(array('required' => 'required')); ?>
                </div>
			</div>


			<div id="acheteur_infos" class="cible">
			<?php if($vrac->acheteur_identifiant): ?>
				<?php include_partial('vrac/soussigne', array('vrac' => $vrac, 'tiers' => $vrac->acheteur, 'fiche' => false)); ?>
			<?php endif; ?>
			</div>
		</div>
		<?php if(isset($form['interlocuteur_commercial'])): ?>
		<?php echo $form['interlocuteur_commercial']->renderError() ?>
		<?php endif; ?>
	</fieldset>
	<?php endif; ?>
	<style type="text/css">
		.bloc_conditionner {
			display: none;
		}
	</style>

	<script type="text/javascript">
	$(document).ready(function () {
		$(".ajouter_annuaire a").click(function() {
			$("#principal").attr('action', $(this).attr('href'));
			$("#principal").submit();
			return false;
		});

		var url_annuaire = "<?php echo url_for('vrac_annuaire', array('sf_subject' => $vrac, 'type' => '-type-', 'acteur' => '-acteur-')) ?>";
		var url_soussigne = "<?php echo url_for('vrac_soussigne_informations', array('sf_subject' => $vrac, 'acteur' => '-acteur-')) ?>";

		$("select.choix_soussigne").combobox();
		<?php if(isset($form['interlocuteur_commercial'])): ?>
		$("#<?php echo $form['interlocuteur_commercial']->renderId() ?>").combobox();
		<?php endif; ?>
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
		<?php if(isset($form['interlocuteur_commercial'])): ?>
		$("#<?php echo $form['interlocuteur_commercial']->renderId() ?>").change(function() {
			if ($(this).val() == 'add') {
				$("#principal").attr('action', '<?php echo url_for('vrac_annuaire_commercial', array('sf_subject' => $vrac)) ?>');
				$("#principal").submit();
				return;
			}
		});
		<?php endif; ?>
		$(".remove_autocomplete").click(function() {
			$(this).parents(".selecteur").siblings(".cible").empty();
		});
	});
	</script>
</div>
