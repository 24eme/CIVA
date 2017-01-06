<div id="nouvelle_declaration">
<?php if(DSSecurity::getInstance($etablissement, $ds, $type_ds)->isAuthorized(DSSecurity::CREATION)): ?>
	<?php include_component('ds', 'monEspaceEnCours', array('type_ds' => $type_ds, 'etablissement' => $etablissement));  ?>
<?php elseif (DSSecurity::getInstance($etablissement, $ds, $type_ds)->isAuthorized(DSSecurity::EDITION)): ?>
	<?php include_component('ds', 'monEspaceEnCours', array('type_ds' => $type_ds, 'etablissement' => $etablissement));  ?>
<?php elseif($ds && $ds->isValideeTiers()): ?>
    <?php include_component('ds', 'monEspaceValidee', array('type_ds' => $type_ds, 'etablissement' => $etablissement));  ?>
<?php elseif(!$sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR) && date('Y-m-d') > DSCivaClient::getInstance()->getDateFermeture()->format('Y-m-d')): ?>
    <?php include_partial('ds/monEspaceNonEditable',array('ds' => $ds, 'type_ds' => $type_ds)); ?>
<?php elseif(!$sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR) && date('Y-m-d') < DSCivaClient::getInstance()->getDateOuverture()->format('Y-m-d')): ?>
    <?php include_partial('ds/monEspaceNonOuvert',array('ds' => $ds, 'type_ds' => $type_ds)); ?>
<?php elseif(!$sf_user->getDeclarantDS($type_ds)->hasLieuxStockage() && !$sf_user->getDeclarantDS($type_ds)->isAjoutLieuxDeStockage()): ?>
    <?php include_component('ds', 'monEspaceNoLieuxStockage', array('type_ds' => $type_ds)); ?>
<?php else: ?>
	<?php include_partial('ds/monEspaceNonEditable',array('ds' => $ds, 'type_ds' => $type_ds)); ?>
<?php endif; ?>
</div>
