<?php include_partial('email/header', array('vrac' => $vrac)); ?>

L'acheteur à validé un projet de contrat pour que le vendeur le signe.

Pour le visualiser et le signer cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
