<?php include_partial('email/header', array('vrac' => $vrac)); ?>

<?php if($vrac->isApplicationPluriannuel()): ?>
Votre signature a bien été prise en compte pour le contrat d'application <?php echo $vrac->campagne  ?>.

Pour consulter le contrat d'application ainsi que le contrat cadre, cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>
<?php else: ?>
Votre signature a bien été prise en compte.

Pour consulter la proposition, cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>
<?php endif; ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
