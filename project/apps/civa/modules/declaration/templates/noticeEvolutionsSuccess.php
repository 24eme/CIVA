<form id="principal" action="" method="post">
<h2 class="titre_principal">Les nouveautés</h2>
<div id="notice_evolutions">
	<?php echo acCouchdbManager::getClient('Messages')->getMessage('dr_notice_evolutions'); ?>
</div>

<?php include_partial('global/boutons', array('display' => array('precedent','suivant'))) ?>
</form>