<?php include_partial('email/_header', array('vrac' => $vrac)); ?>
Ce contrat attend votre signature. Pour le visualiser et le signer cliquez sur le lien suivant : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?><br /><br />
Pour être valable, le contrat doit être signé par toutes les parties et visé par le CIVA. Le PDF correspondant avec le numéro de visa CIVA vous sera alors envoyé par courriel.<br /><br />
Attention si le contrat n'est pas signé par toutes les parties dans les 5 jours à compter de sa date de saisie, il sera automatiquement supprimé.<br /><br />
<?php include_partial('email/_footer', array('vrac' => $vrac)); ?>