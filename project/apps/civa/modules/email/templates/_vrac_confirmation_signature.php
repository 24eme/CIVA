<?php include_partial('email/header', array('vrac' => $vrac)); ?>


Votre signature a bien été prise en compte pour ce contrat.

Pour consulter la proposition, cliquez sur le lien suivant : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>


Le contrat validé en pdf vous sera envoyé après signature de toutes les parties.

Attention ! Entrée en vigueur de la réforme sur des délais de paiement !

Les délais de paiement dérogatoires ne sont plus applicables aux contrats de vins en vrac signés après le 1er juillet 2021 : ceux-ci doivent respecter les délais légaux, soit 60j après la date de facturation. Les contrats annuels et pluri-annuels signés avant le 1er juillet 2021 bénéficient encore de la dérogation jusqu’au 30 juin 2022

<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
