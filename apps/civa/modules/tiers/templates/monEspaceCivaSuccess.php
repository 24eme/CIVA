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

        <div class="clearfix">
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_GAMMA)): ?>
                 <?php include_partial('gamma/monEspace') ?>
            <?php endif; ?>
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ACHETEUR)): ?>
                 <?php include_partial('acheteur/monEspace') ?>
            <?php endif; ?>
            
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_GAMMA)): ?>
                 <?php include_partial('gamma/monEspaceColonne') ?>
            <?php endif; ?>
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

