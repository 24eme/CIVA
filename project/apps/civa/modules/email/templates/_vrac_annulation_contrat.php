<?php include_partial('email/header', array('vrac' => $vrac)); ?>

Ce contrat a été annulé pour le motif suivant :

<?php echo htmlspecialchars_decode($vrac->motif_suppression, ENT_QUOTES) ?>


Néanmoins, il reste consultable sur la plateforme du CIVA, accessible depuis le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
