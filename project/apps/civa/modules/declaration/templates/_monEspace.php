<div id="nouvelle_declaration">
<?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_EN_COURS)): ?>
        <?php include_component('declaration', 'monEspaceEnCours') ?>
<?php elseif($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_VALIDE)): ?>
        <?php include_partial('declaration/monEspaceValidee') ?>
<?php elseif(CurrentClient::getCurrent()->exist('dr_non_ouverte') && CurrentClient::getCurrent()->dr_non_ouverte == 1): ?>
    <?php include_partial('declaration/monEspaceNonOuverte') ?>
<?php else: ?>
    <?php include_partial('declaration/monEspaceNonEditable') ?>
<?php endif; ?>
</div>