<?php include_partial('email/header', array('vrac' => $vrac)); ?>

Une proposition de contrat est en attente de votre signature.

Pour consulter la proposition, cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>


Le contrat validé en pdf vous sera envoyé après signature de toutes les parties.

<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
