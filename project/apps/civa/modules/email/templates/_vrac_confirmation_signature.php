<?php include_partial('email/header', array('vrac' => $vrac)); ?>
Votre signature a bien été prise en compte pour ce contrat.<br /><br />
Pour consulter le contrat, cliquez sur le lien suivant : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?><br /><br />
Le contrat validé en pdf vous sera envoyé après validation de toutes les parties.<br /><br />
<?php include_partial('email/footer', array('vrac' => $vrac)); ?>