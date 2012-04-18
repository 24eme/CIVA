<?php include_partial('global/actions', array('etape' => 0, 'help_popup_action' => $help_popup_action)) ?>

    <h2 class="titre_principal">Mon espace déclaratif</h2>
    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <?php if($sf_user->hasFlash('confirmation')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
        <?php endif; ?>

            
        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION)): ?>
        <div id="espace_alsace_recolte">
            <h2>Alsace récolte</h2>
            <div class="contenu clearfix">  
                 <?php include_component('declaration', 'monEspace') ?>
                 <?php include_component('declaration', 'monEspaceColonne') ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ACHETEUR)): ?>
        <div id="espace_acheteurs">
            <h2>Acheteurs</h2>
            <div class="contenu clearfix">
                 <?php include_component('acheteur', 'monEspace', array('formUploadCsv' => $formUploadCsv)) ?>
            </div>
        </div>
        <?php endif; ?>
             

        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_GAMMA)): ?>
        <div id="espace_gamma">
            <h2>Espace Gamm@</h2>
            <div class="contenu clearfix">
                 <?php include_partial('gamma/monEspace') ?>
                 <?php include_partial('gamma/monEspaceColonne') ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
        <div id="espace_admin">
            <h2>Espace Admin</h2>
            <div class="contenu clearfix">
            </div>
        </div>
        <?php endif; ?>
            
        <!-- #nouvelle_declaration -->
        <?php /*<div id="nouvelle_declaration">
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION)): ?>
                 <?php include_component('declaration', 'monEspace') ?>
                 <br />
            <?php endif; ?>
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_GAMMA)): ?>
                 <?php include_partial('gamma/monEspace') ?>
                 <br />
            <?php endif; ?>
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ACHETEUR)): ?>
                 <?php include_partial('acheteur/monEspace') ?>
                 <br />
            <?php endif; ?>
        </div>
         */ ?>
        <!-- fin #nouvelle_declaration -->

        <!-- #precedentes_declarations -->
       <?php /* <div id="precedentes_declarations">
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION)): ?>
                <?php include_component('declaration', 'monEspaceColonne') ?>
            <?php endif; ?>
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_GAMMA)): ?>
                 <?php include_partial('gamma/monEspaceColonne') ?>
            <?php endif; ?>
        </div>
        */ ?>
        <!-- fin #precedentes_declarations -->
    </div>
    <!-- fin #application_dr -->

