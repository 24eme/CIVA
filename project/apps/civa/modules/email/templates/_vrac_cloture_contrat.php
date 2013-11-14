<?php include_partial('email/header', array('vrac' => $vrac)); ?>
Tous les enlèvements ayant été effectués, ce contrat a été cloturé.

Vous trouverez ci-joint la version pdf avec les volumes rééls.

Nous vous invitons à bien conserver ce document, preuve de la transaction passée entre les différentes parties.

Il sera également accessible sur la plateforme du CIVA à l'adresse suivante : <?php echo sfConfig::get('app_base_url').ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac)); ?>

<?php include_partial('email/footer', array('vrac' => $vrac)); ?>