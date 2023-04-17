<?php include_partial('email/header', array('vrac' => $vrac)); ?>


Ce projet a été refusé par le vendeur. Pour le visualiser et le modifier, cliquez sur le lien suivant : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
