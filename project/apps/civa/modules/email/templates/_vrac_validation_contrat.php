<?php include_partial('email/_header', array('vrac' => $vrac)); ?>
Ce contrat, ayant été validé par toutes les parties, a été visé par le CIVA.<br /><br />
Vous trouverez ci-joint la version pdf avec le numéro de visa suivant <?php echo $vrac->numero_visa ?>.<br /><br />
Dès que tous les enlèvements auront été effectués le contrat sera cloturé et vous recevrez un nouveau mail avec en pièce jointe le contrat définitif comportant les volumes rééls.<br /><br />
Il sera également accessible sur la plateforme du CIVA à l'adresse suivante : <?php echo ProjectConfiguration::getAppRouting()->generate('vrac_fiche', array('sf_subject' => $vrac), true); ?><br /><br />
<?php include_partial('email/_footer', array('vrac' => $vrac)); ?>