<?php include_partial('email/header', array('vrac' => $vrac)); ?>

Votre signature a bien été prise en compte.

Pour consulter la proposition, cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
