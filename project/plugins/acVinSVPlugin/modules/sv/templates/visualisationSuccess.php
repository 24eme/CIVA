<?php use_helper('Float'); ?>

<?php include_partial('sv/breadcrumb', array('sv' => $sv)); ?>

<h2>Déclaration de production <?php echo $sv->campagne ?></h2>

<?php include_partial('document_validation/validationBootstrap', ['validation' => $svvalidation]) ?>

<?php include_partial('sv/recap', ['sv' => $sv]); ?>

<?php include_partial('sv/motifModificationForm', ['sv' => $sv, 'form' => $motifModificationForm]); ?>

<div class="row" style="margin-top: 10px;">
    <div class="col-xs-4"><a href="<?php echo url_for('mon_espace_civa_production', $sv->getEtablissementObject()) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Retourner à mon espace</a></div>
    <div class="col-xs-4 text-center">
      <div class="btn-group dropup">
        <a href="<?php echo url_for('sv_pdf', $sv) ?>" class="btn btn-success"></span> Télécharger le PDF</a>
        <?php if (true): ?>
          <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="caret"></span>
              <span class="sr-only">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu">
            <li><a href="<?php echo url_for('sv_json', $sv) ?>?has_motif=0"> Télécharger le JSON <strong>sans</strong> motif de modification</a></li>
            <li><a href="#" data-toggle="modal" data-target="#sv-json-modal"> Télécharger le JSON <strong>avec</strong> motif de modification</a></li>
          </ul>
        <?php endif ?>
      </div>
    </div>
</div>
