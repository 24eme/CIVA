<?php include_partial('email/header', array('vrac' => $vrac)); ?>
Votre signature a bien été prise en compte pour ce contrat.

Pour consulter le contrat, cliquez sur le lien suivant : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?>

Le contrat validé en pdf vous sera envoyé après validation de toutes les parties.

<?php include_partial('email/footer', array('vrac' => $vrac)); ?>