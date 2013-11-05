<?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION)): ?>
        <div id="espace_alsace_recolte">
            <h2>Alsace Récolte</h2>
            <div class="contenu clearfix">  
                 <?php include_component('declaration', 'monEspace') ?>
                 <?php include_component('declaration', 'monEspaceColonne') ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$sf_user->isInDelegateMode() && $sf_user->hasCredential(myUser::CREDENTIAL_DELEGATION) ): ?>
            <div class="contenu clearfix">
                <?php include_component('tiers', 'delegationForm', array('form' => isset($formDelegation) ? $formDelegation : null)) ?>
            </div>
        <?php endif;?>