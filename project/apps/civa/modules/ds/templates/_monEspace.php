<div id="nouvelle_declaration">
<?php if(DSSecurity::getInstance($sf_user->getRawValue(), $ds)->isAuthorized(DSSecurity::CREATION)): ?>
	<?php include_component('ds', 'monEspaceEnCours');  ?>
<?php elseif (DSSecurity::getInstance($sf_user->getRawValue(), $ds)->isAuthorized(DSSecurity::EDITION)): ?>
	<?php include_component('ds', 'monEspaceEnCours');  ?>
<?php elseif($ds && $ds->isValidee()): ?>
    <?php include_component('ds', 'monEspaceValidee');  ?>
<?php elseif(CurrentClient::getCurrent()->exist('ds_non_editable') && CurrentClient::getCurrent()->ds_non_editable == 1): ?>
    <?php include_partial('ds/monEspaceNonEditable',array('ds' => $ds)); ?>
<?php elseif(CurrentClient::getCurrent()->exist('ds_non_ouverte') && CurrentClient::getCurrent()->ds_non_ouverte == 1): ?>
    <?php include_partial('ds/monEspaceNonOuvert',array('ds' => $ds)); ?>
<?php elseif(!$sf_user->hasLieuxStockage()): ?>
    <?php include_component('ds', 'monEspaceNoLieuxStockage'); ?>
<?php endif; ?>
</div>