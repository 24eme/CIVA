<?php include_partial('email/header', array('vrac' => $vrac)); ?>

Un projet de contrat a été créé par le vendeur et attend la validation de l'acheteur.

Pour le visualiser et le valider cliquez sur le lien suivant : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
