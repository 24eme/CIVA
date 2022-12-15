<?php use_helper('Float'); ?>

<?php include_partial('sv/breadcrumb', array('sv' => $sv)); ?>

<h2>Déclaration de production <?php echo $sv->campagne ?></h2>

<?php include_partial('document_validation/validationBootstrap', ['validation' => $svvalidation]) ?>

<?php include_partial('sv/recap', ['sv' => $sv]); ?>

<div class="row" style="margin-top: 10px;">
    <div class="col-xs-4"><a href="<?php echo url_for('mon_espace_civa_production', $sv->getEtablissementObject()) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Retourner à mon espace</a></div>
    <div class="col-xs-4 text-center"><a href="<?php echo url_for('sv_pdf', $sv) ?>" class="btn btn-success"></span> Télécharger le PDF</a></div>
</div>
