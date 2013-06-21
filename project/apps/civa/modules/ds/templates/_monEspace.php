<div id="nouvelle_declaration">
<?php 
if(!$sf_user->hasCredential(myUser::CREDENTIAL_ADMIN) && (CurrentClient::getCurrent()->exist('ds_non_ouverte') && CurrentClient::getCurrent()->ds_non_ouverte == 1)): 
    include_partial('ds/monEspaceNonOuvert');
    elseif(!$sf_user->hasCredential(myUser::CREDENTIAL_ADMIN) && (CurrentClient::getCurrent()->exist('ds_non_editable') && CurrentClient::getCurrent()->ds_non_editable == 1)): 
        include_partial('ds/monEspaceNonEditable',array('ds' => $ds));
    elseif(!$sf_user->hasLieuxStockage()):
        include_component('ds', 'monEspaceNoLieuxStockage');
    elseif(!$ds->isValidee()):
        include_component('ds', 'monEspaceEnCours');
    else:
        include_component('ds', 'monEspaceValidee'); 
    endif; 
?>
</div>