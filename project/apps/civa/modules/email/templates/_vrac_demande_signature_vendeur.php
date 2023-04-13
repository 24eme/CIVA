<?php include_partial('email/header', array('vrac' => $vrac)); ?>

Un projet de contrat attend votre signature.

Pour le visualiser et le signer cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
