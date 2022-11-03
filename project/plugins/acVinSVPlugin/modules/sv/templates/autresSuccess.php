<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_AUTRES)); ?>

<h3>Autres</h3>

<form action="" method="POST" id="form_autres">
<?php echo $form->renderHiddenFields() ?>
<div class="form-group">
  <?php echo $form['lies']->renderLabel(null, ['class' => 'col-xs-4 control-label']) ?>
  <?php echo $form['lies']->render(['class' => 'form-control']) ?>
</div>

<div class="form-group">
  <?php echo $form['mouts']->renderLabel(null, ['class' => 'col-xs-4 control-label']) ?>
  <?php echo $form['mouts']->render(['class' => 'form-control']) ?>
</div>

<div class="form-group">
  <?php echo $form['rebeches']->renderLabel(null, ['class' => 'col-xs-4 control-label']) ?>
  <?php echo $form['rebeches']->render(['class' => 'form-control']) ?>
</div>

</form>

<div class="row">
  <div class="col-xs-6 text-left"><a href="<?php echo url_for('sv_saisie', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
  <div class="col-xs-6 text-right">
    <button type="submit" form="form_autres" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></button>
  </div>
</div>
