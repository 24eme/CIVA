<?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_EN_COURS)): ?>
        <?php include_component('declaration', 'monEspaceEnCours') ?>
<?php elseif($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_VALIDE)): ?>
        <?php include_partial('declaration/monEspaceValidee') ?>
<?php else: ?>
    <?php include_partial('declaration/monEspaceNonEditable') ?>
<?php endif; ?>

<?php //include_partial('declaration/monEspaceValidee') ?>