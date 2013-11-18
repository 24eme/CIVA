<?php include_partial('global/actions', array('etape' => 0, 'help_popup_action' => $help_popup_action)) ?>

<?php if($sf_user->isInDelegateMode()): ?>
    <h2 class="titre_principal">Espace de <?php echo $sf_user->getCompte(myUser::NAMESPACE_COMPTE_USED)->getNom()?></h2>
<?php else: ?>
    <h2 class="titre_principal">Mon espace déclaratif</h2>
<?php endif; ?>
<div id="application_dr" class="clearfix">
        
        <?php 
        if(CurrentClient::getCurrent()->exist('declaration_courante') && CurrentClient::getCurrent()->declaration_courante == 'DR'):
            include_partial('tiers/monEspaceDr',array('sf_user' => $sf_user, 'formDelegation' => isset($formDelegation) ? $formDelegation : null));
        else:
            include_partial('tiers/monEspaceDs',array('sf_user' => $sf_user));
        endif;
        ?>
        
        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ACHETEUR)): ?>
        <div id="espace_acheteurs">
            <h2>Acheteurs</h2>
            <div class="contenu clearfix">
                 <?php include_component('acheteur', 'monEspace', array('formUploadCsv' => $formUploadCsv)) ?>
            </div>
        </div>
        <?php endif; ?>   
        
        <?php include_component('vrac', 'monEspace') ?>          

        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_GAMMA)): ?>
        <div id="espace_gamma">
            <h2>Espace Gamm@</h2>
            <div class="contenu clearfix">
                 <?php include_partial('gamma/monEspace') ?>
                 <?php include_partial('gamma/monEspaceColonne') ?>
            </div>
        </div>
        <?php endif; ?>
            
        <?php 
        if(CurrentClient::getCurrent()->exist('declaration_courante') && CurrentClient::getCurrent()->declaration_courante == 'DR'):
            include_partial('tiers/monEspaceDs',array('sf_user' => $sf_user));
        else:
            include_partial('tiers/monEspaceDr',array('sf_user' => $sf_user, 'formDelegation' => isset($formDelegation) ? $formDelegation : null));
        endif;
        ?>      
    </div>

