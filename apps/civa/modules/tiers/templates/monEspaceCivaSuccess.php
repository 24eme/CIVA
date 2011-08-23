<?php include_partial('global/actions', array('etape' => 0, 'help_popup_action' => $help_popup_action)) ?>

    <h2 class="titre_principal">Mon espace CIVA</h2>
    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <?php if($sf_user->hasFlash('mdp_modif')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('mdp_modif'); ?></p>
        <?php endif; ?>

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
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
        <!-- fin #nouvelle_declaration -->

        <!-- #precedentes_declarations -->
        <div id="precedentes_declarations">
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION)): ?>
                <?php include_component('declaration', 'monEspaceColonne') ?>
            <?php endif; ?>
            <?php if($sf_user->hasCredential(myUser::CREDENTIAL_GAMMA)): ?>
                 <?php include_partial('gamma/monEspaceColonne') ?>
            <?php endif; ?>
        </div>
        <!-- fin #precedentes_declarations -->
    </div>
    <!-- fin #application_dr -->

