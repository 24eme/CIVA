<div id="nouvelle_declaration">
<?php if(DSSecurity::getInstance($sf_user->getRawValue(), $ds)->isAuthorized(DSSecurity::CREATION)): ?>
	<?php include_component('ds', 'monEspaceEnCours', array('type_ds' => $type_ds));  ?>
<?php elseif (DSSecurity::getInstance($sf_user->getRawValue(), $ds)->isAuthorized(DSSecurity::EDITION)): ?>
	<?php include_component('ds', 'monEspaceEnCours', array('type_ds' => $type_ds));  ?>
<?php elseif($ds && $ds->isValidee()): ?>
    <?php include_component('ds', 'monEspaceValidee', array('type_ds' => $type_ds));  ?>
<?php elseif(!$sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR) && CurrentClient::getCurrent()->exist('ds_non_editable') && CurrentClient::getCurrent()->ds_non_editable == 1): ?>
    <?php include_partial('ds/monEspaceNonEditable',array('ds' => $ds, 'type_ds' => $type_ds)); ?>
<?php elseif(!$sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR) && CurrentClient::getCurrent()->exist('ds_non_ouverte') && CurrentClient::getCurrent()->ds_non_ouverte == 1): ?>
    <?php include_partial('ds/monEspaceNonOuvert',array('ds' => $ds, 'type_ds' => $type_ds)); ?>
<?php elseif(!$sf_user->getDeclarantDS($type_ds)->hasLieuxStockage() && !$sf_user->getDeclarantDS($type_ds)->isAjoutLieuxDeStockage()): ?>    
    <?php include_component('ds', 'monEspaceNoLieuxStockage', array('type_ds' => $type_ds)); ?>
<?php endif; ?>
</div>