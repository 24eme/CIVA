<div class="clearfix">
	<div>
		<h1>Vendeur</h1><br />
		<div class="bloc_condition" data-condition-cible="#vendeur_recoltants|#vendeur_negociants|#vendeur_caves_cooperatives">
			Type : <?php echo $form['vendeur_type']->render() ?>
		</div>
		Nom / CVI * :	
		<div id="vendeur_recoltants" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY ?>">
			<?php echo $form['vendeur_recoltant_identifiant']->render() ?>
			<a href="<?php echo url_for('annuaire_selectionner', array('type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY)) ?>">Ajouter un contact</a>
		</div>
		<div id="vendeur_negociants" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
			<?php echo $form['vendeur_negociant_identifiant']->render() ?>
			<a href="<?php echo url_for('annuaire_selectionner', array('type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY)) ?>">Ajouter un contact</a>
		</div>
		<div id="vendeur_caves_cooperatives" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
			<?php echo $form['vendeur_cave_cooperative_identifiant']->render() ?>
			<a href="<?php echo url_for('annuaire_selectionner', array('type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY)) ?>">Ajouter un contact</a>
		</div>
		<span><?php echo $form['vendeur_recoltant_identifiant']->renderError() ?></span>
		<span><?php echo $form['vendeur_negociant_identifiant']->renderError() ?></span>
		<span><?php echo $form['vendeur_cave_cooperative_identifiant']->renderError() ?></span>
		<div id="vendeur_infos" class="informations">
			<?php 
				if ($vrac->vendeur_identifiant) {
					include_partial('vrac/soussigne', array('tiers' => $vrac->vendeur));
				} 
			?>
		</div>
	</div>
	<br />
	<div>
		<h1>Acheteur</h1><br />
		<div class="bloc_condition" data-condition-cible="#acheteur_recoltants|#acheteur_negociants|#acheteur_caves_cooperatives">
			Type : <?php echo $form['acheteur_type']->render() ?>
		</div>
		Nom / CVI * :	
		<div id="acheteur_recoltants" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY ?>">
			<?php echo $form['acheteur_recoltant_identifiant']->render() ?>
			<a href="<?php echo url_for('annuaire_selectionner', array('type' => AnnuaireClient::ANNUAIRE_RECOLTANTS_KEY)) ?>">Ajouter un contact</a>
		</div>
		<div id="acheteur_negociants" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
			<?php echo $form['acheteur_negociant_identifiant']->render() ?>
			<a href="<?php echo url_for('annuaire_selectionner', array('type' => AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY)) ?>">Ajouter un contact</a>
		</div>
		<div id="acheteur_caves_cooperatives" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
			<?php echo $form['acheteur_cave_cooperative_identifiant']->render() ?>
			<a href="<?php echo url_for('annuaire_selectionner', array('type' => AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY)) ?>">Ajouter un contact</a>
		</div>
		<span><?php echo $form['acheteur_recoltant_identifiant']->renderError() ?></span>
		<span><?php echo $form['acheteur_negociant_identifiant']->renderError() ?></span>
		<span><?php echo $form['acheteur_cave_cooperative_identifiant']->renderError() ?></span>
		<div id="acheteur_infos" class="informations">
			<?php 
				if ($vrac->acheteur_identifiant) {
					include_partial('vrac/soussigne', array('tiers' => $vrac->acheteur));
				} 
			?>
		</div>
	</div>
	<br />
	<div>
		<h1>Courtier</h1><br />
		<div>
			<p>Veuillez selectionner ou saisir l'identité de l'interlocuteur commercial en charge de ce contrat de vente en vrac :</p>
			<?php echo $form['interlocuteur_commercial']->render() ?>
			<a href="<?php echo url_for('@annuaire_commercial_ajouter') ?>">Ajouter un contact</a>
		</div>
		<span><?php echo $form['interlocuteur_commercial']->renderError() ?></span>
	</div>
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
				return document.location.href="<?php echo url_for('annuaire_selectionner', array('type' => 'recoltants')) ?>";
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#vendeur_infos').empty(); $('#vendeur_infos').append(data);});
		});
		$("#<?php echo $form['vendeur_negociant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('annuaire_selectionner', array('type' => 'negociants')) ?>"; 
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#vendeur_infos').empty(); $('#vendeur_infos').append(data);});
		});
		$("#<?php echo $form['vendeur_cave_cooperative_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('annuaire_selectionner', array('type' => 'caves_cooperatives')) ?>"; 
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#vendeur_infos').empty(); $('#vendeur_infos').append(data);});
		});
		$("#<?php echo $form['acheteur_recoltant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('annuaire_selectionner', array('type' => 'recoltants')) ?>";
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#acheteur_infos').empty(); $('#acheteur_infos').append(data);});
		});
		$("#<?php echo $form['acheteur_negociant_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('annuaire_selectionner', array('type' => 'negociants')) ?>"; 
			}
			$.post("<?php echo url_for('vrac_soussigne_informations') ?>", { identifiant: $(this).val() }, function(data) {$('#acheteur_infos').empty(); $('#acheteur_infos').append(data);});
		});
		$("#<?php echo $form['acheteur_cave_cooperative_identifiant']->renderId() ?>").change(function() { 
			if ($(this).val() == 'add') { 
				return document.location.href="<?php echo url_for('annuaire_selectionner', array('type' => 'caves_cooperatives')) ?>"; 
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
	

