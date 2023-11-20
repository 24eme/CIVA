<div class="modal modal-overflow" id="modal_ajout_apporteur" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <a href="<?php echo url_for('sv_apporteurs', $sv) ?>"  class="close"><span aria-hidden="true">&times;</span></a>
        <h3 class="modal-title" id="gridSystemModalLabel">Ajouter un apporteur</h3>
      </div>
      <div class="modal-body">
        <form action="<?php echo url_for('sv_ajout_apporteur', ['sf_subject' => $sv]) ?><?php echo (! $hasCVI) ? '' : "?addCVI=$hasCVI" ?>" method="POST" id="form_ajout_apporteur" class="form-horizontal">
        <?php echo $form->renderHiddenFields() ?>
        <?php echo $form->renderGlobalErrors() ?>
          <div class="form-group">
            <?php echo $form['cvi']->renderLabel(null, ['class' => 'control-label col-xs-5']) ?>
            <div class="col-xs-7">
              <?php echo $form['cvi']->render(['class' => 'form-control']) ?>
              <small  style="margin-bottom: 0;" class="help-block">Numéro CVI pour la France ou N° TVA Intra. pour l'étranger</small>
            </div>
          </div>

          <?php if (isset($form['raison_sociale'])): ?>
          <div class="form-group">
              <?php echo $form['raison_sociale']->renderLabel(null, ['class' => 'control-label col-xs-5']) ?>
              <div class="col-xs-7">
                  <?php echo $form['raison_sociale']->render(['class' => 'form-control']) ?>
              </div>
          </div>
          <div class="form-group">
              <?php echo $form['pays']->renderLabel(null, ['class' => 'control-label col-xs-5']) ?>
              <div class="col-xs-7">
                  <?php echo $form['pays']->render(['class' => 'form-control']) ?>
              </div>
          </div>
          <?php endif ?>
        </form>
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-xs-6 text-left">
            <a href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-default">Annuler</a>
          </div>
          <div class="col-xs-6">
            <button form="form_ajout_apporteur" type="submit" class="btn btn-success">Enregistrer</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
