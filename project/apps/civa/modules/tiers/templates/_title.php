<?php if($sf_user->isInDelegateMode()): ?>
    <h2 class="titre_principal">Espace de <?php echo $sf_user->getCompte(myUser::NAMESPACE_COMPTE_USED)->getNomAAfficher()?><?php if($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?> <a href="<?php echo url_for("admin") ?>" style="opacity: 0.80; font-size: 10px; color: white; text-transform: uppercase;">Changer</a><?php endif; ?></h2>
<?php else: ?>
    <h2 class="titre_principal">Mon espace déclaratif</h2>
<?php endif; ?>
