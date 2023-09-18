<?php use_helper('Float'); ?>

<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_VALIDATION)); ?>

<h2>Validation de la déclaration de production <?php echo $sv->campagne ?></h2>

<p style="margin-bottom: 15px;">Veuillez vérifier les informations saisies avant de valider votre déclaration. Vous pouvez à tout moment visualiser votre déclaration de production au format PDF en cliquant sur le bouton "Prévisualiser' en bas de l'écran.</p>

<?php include_partial('document_validation/validationBootstrap', ['validation' => $svvalidation]) ?>

<?php include_partial('sv/recap', ['sv' => $sv]); ?>

<div class="row" style="margin-top: 10px;">
    <div class="col-xs-4 text-left"><a href="<?php echo url_for('sv_stockage', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
    <div class="col-xs-4 text-center"><a href="<?php echo url_for('sv_pdf', $sv) ?>" class="btn btn-success"></span> Télécharger le PDF</a></div>
    <div class="col-xs-4 text-right"><button id="valideSV" class="btn btn-success"<?php echo ($svvalidation->isValide()) ? '' : 'disabled' ?>>Terminer la saisie <span class="glyphicon glyphicon glyphicon-ok"></span></button></div>
  <?php include_partial('sv/popup_validation', ['sv' => $sv]); ?>
</div>
