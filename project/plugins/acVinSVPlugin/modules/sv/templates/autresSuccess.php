<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_AUTRES)); ?>

<h3>Autres</h3>

<p>Texte d'intro</p>

<form action="" method="POST" id="form_autres" class="form-horizontal">
<?php echo $form->renderHiddenFields() ?>
<div class="form-group">
  <?php echo $form['lies']->renderLabel(null, ['style' => '', 'class' => 'col-xs-3 control-label']) ?>
  <div class="input-group col-xs-3">
    <?php echo $form['lies']->render(['class' => 'form-control input-float text-right']) ?>
    <div class="input-group-addon">hl</div>
  </div>
</div>

</form>

<div class="row">
  <div class="col-xs-6 text-left"><a tabindex="-1" href="<?php echo url_for('sv_saisie', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
  <div class="col-xs-6 text-right">
    <button type="submit" form="form_autres" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></button>
  </div>
</div>
