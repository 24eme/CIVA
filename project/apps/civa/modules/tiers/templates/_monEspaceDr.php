<?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION)): ?>
        <div id="espace_alsace_recolte">
            <h2>Alsace récolte</h2>
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

        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
        <div id="espace_admin">
            <h2>Espace Admin</h2>
            <div class="contenu clearfix">
                <a  href="<?php echo url_for('@migration_compte')?>">Migration compte</a>

            </div>
        </div>
        <?php endif; ?>