<?php include_partial('email/header', array('vrac' => $vrac)); ?>


Cette proposition attend votre validation. Pour la visualiser et la valider cliquez sur le lien suivant : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?>


Pour être valable, le contrat doit être signé par toutes les parties et visé par le CIVA. Le PDF correspondant avec le numéro de visa CIVA vous sera alors envoyé par courriel.

Attention si le contrat n'est pas signé par toutes les parties dans les 5 jours à compter de sa date de saisie, il sera automatiquement supprimé.

Attention ! Entrée en vigueur de la réforme sur des délais de paiement !

Les délais de paiement dérogatoires ne sont plus applicables aux contrats de vins en vrac signés après le 1er juillet 2021 : ceux-ci doivent respecter les délais légaux, soit 60j après la date de facturation. Les contrats annuels et pluri-annuels signés avant le 1er juillet 2021 bénéficient encore de la dérogation jusqu’au 30 juin 2022

<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
