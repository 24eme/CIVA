<div class="modal" id="sv-json-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3>Objet de la modification :</h3>
      </div>
      <div class="modal-body">
        <form action="<?php echo url_for('sv_json', $sv) ?>" method="post" id="form_sv_motif_modification">
          <?php echo $form->renderHiddenFields() ?>
          <?php echo $form->renderGlobalErrors() ?>
          <div class="row">
            <div class="col-xs-12">
              <?php echo $form['type']->render() ?>
            </div>
            <div class="col-xs-12">
              <?php echo $form['motif']->renderLabel() ?>
              <?php echo $form['motif']->render(['class' => 'form-control']); ?>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="text-align-left btn btn-default" data-dismiss="modal">Fermer</button>
        <button type="submit" form="form_sv_motif_modification" class="btn btn-success">Export</button>
      </div>
    </div>
  </div>
</div>
