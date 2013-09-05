<div>
	<h1>Vendeur</h1><br />
	<?php echo $form['vendeur_identifiant']->renderLabel() ?>	
	<?php echo $form['vendeur_identifiant']->render() ?>
	<span><?php echo $form['vendeur_identifiant']->renderError() ?></span>
</div>
<br />
<div>
	<h1>Acheteur</h1><br />
	<?php echo $form['acheteur_identifiant']->renderLabel() ?>	
	<?php echo $form['acheteur_identifiant']->render() ?>
	<span><?php echo $form['acheteur_identifiant']->renderError() ?></span>
</div>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
	$("#<?php echo $form['vendeur_identifiant']->renderId() ?>").combobox();
	$("#<?php echo $form['acheteur_identifiant']->renderId() ?>").combobox();
}, false);
</script>