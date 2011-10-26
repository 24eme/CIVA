<?php include_partial('global/actions', array('etape' => 0, 'help_popup_action' => $help_popup_action)) ?>

    <h2 class="titre_principal">Mon espace CIVA</h2>
    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <?php if($sf_user->hasFlash('mdp_modif')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('mdp_modif'); ?></p>
        <?php endif; ?>

            
        <div id="espace_alsace_recolte">
            <h2>Alsace récolte</h2>
            <div class="contenu clearfix">  
                <?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION)): ?>
                 <?php include_component('declaration', 'monEspace') ?>
                <?php endif; ?>

                <?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION)): ?>
                    <?php include_component('declaration', 'monEspaceColonne') ?>
                <?php endif; ?>
            </div>
        </div>
            
        <div id="espace_acheteurs">
            <h2>Acheteurs</h2>
            <div class="contenu clearfix">
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ACHETEUR)): ?>
                 <?php include_partial('acheteur/monEspace') ?>
            <?php endif; ?>
            </div>
        </div>
        
        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
        <div id="espace_admin">
            <h2>Espace Admin</h2>
            <div class="contenu clearfix">
            </div>
        </div>
        <?php endif; ?>
             

        <div id="espace_gamma">
            <h2>Espace Gamm@</h2>
            <div class="contenu clearfix">
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_GAMMA)): ?>
                 <?php include_partial('gamma/monEspace') ?>
            <?php endif; ?>
            
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_GAMMA)): ?>
                 <?php include_partial('gamma/monEspaceColonne') ?>
            <?php endif; ?>
            </div>
        </div>
            
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

