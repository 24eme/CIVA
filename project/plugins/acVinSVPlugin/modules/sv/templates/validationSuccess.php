<?php use_helper('Float'); ?>

<?php if (! $sv->isValide()): ?>
  <?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_VALIDATION)); ?>
<?php endif ?>

<?php include_partial('sv/points', ['validation' => $svvalidation]) ?>

<h3>Récapitulatif par produit</h3>

<p>Texte d'intro</p>

<?php if ($sv->getType() === SVClient::TYPE_SV11): ?>
  <?php include_partial('sv/validationSV11', ['sv' => $sv]); ?>
<?php else: ?>
  <?php include_partial('sv/validationSV12', ['sv' => $sv]); ?>
<?php endif ?>

<?php include_partial('sv/validationLies', ['sv' => $sv]); ?>

<?php if ($sv->isValide()): ?>
  <div class="row">
    <div class="text-center"><a href="<?php echo url_for('sv_pdf', $sv) ?>" class="btn btn-success"></span> Télécharger le PDF</a></div>
  </div>
<?php else: ?>
  <div class="row">
    <div class="col-xs-4 text-left"><a href="<?php echo url_for('sv_stockage', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
    <div class="col-xs-4 text-center"><a href="<?php echo url_for('sv_pdf', $sv) ?>" class="btn btn-success"></span> Télécharger le PDF</a></div>
    <div class="col-xs-4 text-right"><button id="valideSV" class="btn btn-success">Terminer la saisie <span class="glyphicon glyphicon glyphicon-ok"></span></button></div>
  </div>

  <?php include_partial('sv/popup_validation', ['sv' => $sv]); ?>
<?php endif ?>
