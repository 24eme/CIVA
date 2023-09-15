<hr/>
<h3>Objet de la modification :</h3>

<form action="" method="post" id="form_sv_motif_modification">
  <?php echo $form->renderHiddenFields() ?>
  <?php echo $form->renderGlobalErrors() ?>
  <div class="row">
    <div class="col-xs-6">
      <?php echo $form['type']->render() ?>
    </div>
    <div class="col-xs-6">
      <?php echo $form['motif']->renderLabel() ?>
      <?php echo $form['motif']->render(['class' => 'form-control']); ?>
    </div>
  </div>
</form>
