<?php if ($sf_user->hasFlash('confirmation')) : ?>
    <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
<?php endif; ?>

<?php if ($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION)): ?>
    <div id="espace_alsace_recolte">
        <h2>Alsace stock</h2>
        <div class="contenu clearfix">  
            <?php include_component('ds', 'monEspace') ?>
            <?php include_component('ds', 'monEspaceColonne') ?>
        </div>
    </div>
<?php endif; ?>