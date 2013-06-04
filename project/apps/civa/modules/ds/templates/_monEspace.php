<div id="nouvelle_declaration">
<?php // if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_EN_COURS)): ?>
        <?php include_component('ds', 'monEspaceEnCours'); ?>
<?php //elseif($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_VALIDE)): ?>
        <?php //include_partial('ds/monEspaceValidee') ?>
<?php //if(CurrentClient::getCurrent()->exist('ds_non_ouverte') && CurrentClient::getCurrent()->dr_non_ouverte == 1): ?>
    <?php //include_partial('ds/monEspaceNonOuverte') ?>
<?php //else: ?>
    <?php //include_partial('ds/monEspaceNonEditable') ?>
<?php //endif; ?>
</div>