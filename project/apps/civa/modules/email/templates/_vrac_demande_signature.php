<?php include_partial('email/header', array('vrac' => $vrac)); ?>

<?php if($vrac->isApplicationPluriannuel()): ?>
Le contrat d'application pour la campagne <?php echo $vrac->campagne  ?> est en attente de votre signature.

Pour consulter le contrat d'application ainsi que le contrat cadre, cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>
<?php else: ?>
Une proposition de contrat est en attente de votre signature.

Pour consulter la proposition, cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>
<?php endif; ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
