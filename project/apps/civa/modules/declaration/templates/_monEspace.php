<div id="nouvelle_declaration">
<?php if (DRSecurity::getInstance($sf_user, $sf_user->getDeclaration())->isAuthorized(DRSecurity::EDITION)): ?>
	<?php include_component('declaration', 'monEspaceEnCours') ?>
<?php elseif ($sf_user->getDeclaration() && $sf_user->isDRValidee()): ?>
    <?php include_partial('declaration/monEspaceValidee') ?>
<?php elseif(CurrentClient::getCurrent()->exist('dr_non_ouverte') && CurrentClient::getCurrent()->dr_non_ouverte == 1): ?>
    <?php include_partial('declaration/monEspaceNonOuverte') ?>
<?php else: ?>
    <?php include_partial('declaration/monEspaceNonEditable') ?>
<?php endif; ?>
</div>