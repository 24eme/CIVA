<?php use_helper('Float'); ?>

<?php if (! $sv->isValide()): ?>
  <?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_VALIDATION)); ?>
<?php endif ?>

<?php if ($sv->isValide()): ?>
<h3>Visualisation de la déclaration de production <?php echo $sv->campagne ?></h3>

<p style="margin-bottom: 15px;">Texte intro</p>
<?php else: ?>
<h3>Validation de la déclaration de production <?php echo $sv->campagne ?></h3>

<p style="margin-bottom: 15px;">Texte intro</p>
<?php endif; ?>

<?php include_partial('document_validation/validationBootstrap', ['validation' => $svvalidation]) ?>

<h3>Récapitulatif</h3>

<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active"><a href="#recap-produit" aria-controls="recap-produit" role="tab" data-toggle="tab">Récapitulatif par produit</a></li>
  <li role="presentation"><a href="#recap-stockage" aria-controls="recap-stockage" role="tab" data-toggle="tab">Répartition par lieux de stockage</a></li>
</ul>

<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="recap-produit">
    <?php if ($sv->getType() === SVClient::TYPE_SV11): ?>
      <?php include_partial('sv/validationSV11', ['sv' => $sv]); ?>
    <?php else: ?>
      <?php include_partial('sv/validationSV12', ['sv' => $sv]); ?>
    <?php endif ?>
  </div>

  <div role="tabpanel" class="tab-pane" id="recap-stockage">
    <?php include_partial('sv/validationStockage', ['sv' => $sv]); ?>
  </div>
</div>

<?php include_partial('sv/validationLies', ['sv' => $sv]); ?>

<div class="row" style="margin-top: 10px;">
<?php if ($sv->isValide()): ?>
    <div class="text-center"><a href="<?php echo url_for('sv_pdf', $sv) ?>" class="btn btn-success"></span> Télécharger le PDF</a></div>
<?php else: ?>
    <div class="col-xs-4 text-left"><a href="<?php echo url_for('sv_stockage', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
    <div class="col-xs-4 text-center"><a href="<?php echo url_for('sv_pdf', $sv) ?>" class="btn btn-success"></span> Télécharger le PDF</a></div>
    <div class="col-xs-4 text-right"><button id="valideSV" class="btn btn-success"<?php echo ($svvalidation->isValide()) ? '' : 'disabled' ?>>Terminer la saisie <span class="glyphicon glyphicon glyphicon-ok"></span></button></div>
  <?php include_partial('sv/popup_validation', ['sv' => $sv]); ?>
<?php endif ?>
</div>
