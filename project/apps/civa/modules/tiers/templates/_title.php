<?php if($sf_user->isInDelegateMode()): ?>
    <h2 class="titre_principal">Espace de <?php echo $sf_user->getCompte(myUser::NAMESPACE_COMPTE_USED)->getNom()?></h2>
<?php else: ?>
    <h2 class="titre_principal">Mon espace déclaratif</h2>
<?php endif; ?>