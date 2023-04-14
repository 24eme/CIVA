<?php include_partial('email/header', array('vrac' => $vrac)); ?>

Ce contrat, ayant été signé par toutes les parties, a été visé par le CIVA.

<?php if ($vrac->declaration->hashProduitsWithVolumeBloque()): ?>
Attention ! Ce contrat contient des produits dont une partie du volume est en réserve.

<?php endif; ?>
Vous trouverez ci-joint la version pdf avec le numéro de visa suivant <?php echo $vrac->numero_visa ?>.

<?php if($vrac->needRetiraison()): ?>
Dès que tous les enlèvements auront été effectués le contrat sera clôturé et vous recevrez un nouveau mail avec en pièce jointe le contrat définitif comportant les volumes rééls.
<?php else: ?>
Nous vous invitons à bien conserver ce document, preuve de la transaction passée entre les différentes parties.
<?php endif; ?>

Il sera également accessible sur la plateforme du CIVA à l'adresse suivante : <?php echo url_for('vrac_fiche', array('sf_subject' => $vrac), true); ?>


<?php include_partial('email/footer', array('vrac' => $vrac)); ?>
