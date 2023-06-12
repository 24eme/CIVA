<?php include_partial('email/header', array('vrac' => $vrac)); ?>

Le contrat d'application pour la campagne <?php echo $vrac->campagne ?> a été refusé par le vendeur, il a donc été supprimé.

Pour consulter le contrat cadre, cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac->getContratPluriannuelCadre()), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
