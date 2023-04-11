<?php include_partial('email/header', array('vrac' => $vrac)); ?>


Ce contrat, ayant été signé par toutes les parties, a été visé par le CIVA.

Vous trouverez ci-joint la version pdf avec le numéro de visa suivant <?php echo $vrac->numero_visa ?>.

Nous vous invitons à bien conserver ce document, preuve de la transaction passée entre les différentes parties.

Il sera également accessible sur la plateforme du CIVA à l'adresse suivante : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>