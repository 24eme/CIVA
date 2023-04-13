<?php include_partial('email/header', array('vrac' => $vrac)); ?>

Un projet de contrat a été créé par <?php echo  trim($vrac->vendeur->intitule.' '.$vrac->vendeur->raison_sociale) ?> et attend votre validation.

Pour le visualiser et le valider cliquez sur le lien suivant : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
