<?php include_partial('email/header', array('vrac' => $vrac)); ?>
Ce contrat a été annulé pour le motif &laquo; <?php echo $vrac->motif_suppression ?> &raquo;.<br /><br />
Néanmoins, il reste consultable sur la plateforme du CIVA, accessible depuis le lien suivant : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?><br /><br />
<?php include_partial('email/footer', array('vrac' => $vrac)); ?>