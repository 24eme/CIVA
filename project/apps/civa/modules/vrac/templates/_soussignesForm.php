<div>
	<h1>Vendeur</h1><br />
	<div class="bloc_condition" data-condition-cible="#vendeur_recoltants|#vendeur_negociants|#vendeur_caves_cooperatives">
		<?php echo $form['vendeur_type']->render() ?>
	</div>
	CVI * :	
	<div id="vendeur_recoltants" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANT_KEY ?>">
		<?php echo $form['vendeur_recoltant_identifiant']->render() ?>
	</div>
	<div id="vendeur_negociants" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
		<?php echo $form['vendeur_negociant_identifiant']->render() ?>
	</div>
	<div id="vendeur_caves_cooperatives" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
		<?php echo $form['vendeur_cave_cooperative_identifiant']->render() ?>
	</div>
	<span><?php echo $form['vendeur_recoltant_identifiant']->renderError() ?></span>
	<span><?php echo $form['vendeur_negociant_identifiant']->renderError() ?></span>
	<span><?php echo $form['vendeur_cave_cooperative_identifiant']->renderError() ?></span>
</div>
<br />
<div>
	<h1>Acheteur</h1><br />
	<div class="bloc_condition" data-condition-cible="#acheteur_recoltants|#acheteur_negociants|#acheteur_caves_cooperatives">
		<?php echo $form['acheteur_type']->render() ?>
	</div>
	CVI * :	
	<div id="acheteur_recoltants" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_RECOLTANT_KEY ?>">
		<?php echo $form['acheteur_recoltant_identifiant']->render() ?>
	</div>
	<div id="acheteur_negociants" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_NEGOCIANTS_KEY ?>">
		<?php echo $form['acheteur_negociant_identifiant']->render() ?>
	</div>
	<div id="acheteur_caves_cooperatives" class="bloc_conditionner" data-condition-value="<?php echo AnnuaireClient::ANNUAIRE_CAVES_COOPERATIVES_KEY ?>">
		<?php echo $form['acheteur_cave_cooperative_identifiant']->render() ?>
	</div>
	<span><?php echo $form['acheteur_recoltant_identifiant']->renderError() ?></span>
	<span><?php echo $form['acheteur_negociant_identifiant']->renderError() ?></span>
	<span><?php echo $form['acheteur_cave_cooperative_identifiant']->renderError() ?></span>
</div>
<style type="text/css">
.bloc_conditionner {
	display: none;
}
</style>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
	$("#<?php echo $form['vendeur_recoltant_identifiant']->renderId() ?>").combobox();
	$("#<?php echo $form['vendeur_negociant_identifiant']->renderId() ?>").combobox();
	$("#<?php echo $form['vendeur_cave_cooperative_identifiant']->renderId() ?>").combobox();
	$("#<?php echo $form['acheteur_recoltant_identifiant']->renderId() ?>").combobox();
	$("#<?php echo $form['acheteur_negociant_identifiant']->renderId() ?>").combobox();
	$("#<?php echo $form['acheteur_cave_cooperative_identifiant']->renderId() ?>").combobox();
}, false);
</script>