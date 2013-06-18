<div id="nouvelle_declaration">
<?php if(!$ds->isValidee()):
        include_component('ds', 'monEspaceEnCours');
    else:
        include_component('ds', 'monEspaceValidee'); 
    endif; 
?>
        <?php //include_partial('ds/monEspaceValidee') ?>
<?php //if(CurrentClient::getCurrent()->exist('ds_non_ouverte') && CurrentClient::getCurrent()->dr_non_ouverte == 1): ?>
    <?php //include_partial('ds/monEspaceNonOuverte') ?>
<?php //else: ?>
    <?php //include_partial('ds/monEspaceNonEditable') ?>
<?php //endif; ?>
</div>