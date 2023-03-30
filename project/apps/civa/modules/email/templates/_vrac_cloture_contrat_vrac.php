<?php include_partial('email/header', array('vrac' => $vrac)); ?>


Tous les enlèvements ayant été effectués, ce contrat a été cloturé.

<?php if ($vrac->declaration->hashProduitsWithVolumeBloque()): ?>
Attention ! Ce contrat contient des produits dont une partie du volume est en réserve.

<?php endif; ?>
Vous trouverez ci-joint la version pdf avec les volumes rééls.

Nous vous invitons à bien conserver ce document, preuve de la transaction passée entre les différentes parties.

Il sera également accessible sur la plateforme du CIVA à l'adresse suivante : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>

Attention ! Entrée en vigueur de la réforme sur des délais de paiement !

Les délais de paiement dérogatoires ne sont plus applicables aux contrats de vins en vrac signés après le 1er juillet 2021 : ceux-ci doivent respecter les délais légaux, soit 60j après la date de facturation. Les contrats annuels et pluri-annuels signés avant le 1er juillet 2021 bénéficient encore de la dérogation jusqu’au 30 juin 2022

<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
