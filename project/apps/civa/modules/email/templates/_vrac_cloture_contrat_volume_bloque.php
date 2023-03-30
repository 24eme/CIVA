<?php include_partial('email/header', array('vrac' => $vrac)); ?>


Tous les enlèvements ayant été effectués, ce contrat a été cloturé : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?>

Attention ! Ce contrat contient des produits dont une partie du volume est en réserve !

<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
