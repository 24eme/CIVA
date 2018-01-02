<?php include_partial('email/header', array('vrac' => $vrac)); ?>

Ce contrat a été enregistré au CIVA avec le numéro de visa <?php echo $vrac->numero_visa ?>.

Il sera également accessible sur la plateforme du CIVA à l'adresse suivante : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>


Pour toutes questions, vous pouvez contacter le CIVA.

--
Marco RIBEIRO
Responsable des Contrats de Vente
