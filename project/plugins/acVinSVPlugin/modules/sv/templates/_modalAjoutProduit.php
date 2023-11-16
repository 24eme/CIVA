<div class="modal modal-overflow" id="modal_ajout_produit" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title" id="gridSystemModalLabel">Ajouter un produit</h3>
      </div>
      <div class="modal-body">
        <form action="<?php echo url_for('sv_ajout_produit_apporteur', ['id' => $sv->_id, 'cvi' => $cvi]) ?>" method="POST" id="form_ajout_produit_apporteur">
        <?php echo $form->renderHiddenFields() ?>
        <?php echo $form->renderGlobalErrors() ?>
          <div class="form-group">
            <?php echo $form['produit']->renderLabel() ?>
            <?php echo $form['produit']->render(['class' => 'form-control']) ?>
          </div>
          <div class="form-group">
            <?php echo $form['denomination_complementaire']->renderLabel() ?>
            <?php echo $form['denomination_complementaire']->render(['class' => 'form-control']) ?>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-xs-6 text-left">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          </div>
          <div class="col-xs-6">
            <button form="form_ajout_produit_apporteur" type="submit" class="btn btn-success">Ajouter</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

