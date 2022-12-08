<?php use_helper('Float'); ?>

<div class="modal show modal-overflow" id="modal_taux_extraction" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <a href="<?php echo url_for('sv_apporteurs', $sv) ?>"  class="close"><span aria-hidden="true">&times;</span></a>
        <h4 class="modal-title" id="gridSystemModalLabel">Paramétrage des taux d'extraction globaux</h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo url_for('sv_extraction', $sv) ?>" method="POST" id="form_extraction">
        <?php echo $form->renderHiddenFields() ?>
        <?php echo $form->renderGlobalErrors() ?>
        <table class="table table-bordered table-striped table-condensed" style="margin-bottom: 0;">
          <thead>
            <tr>
              <th class="col-xs-10">Produit</th>
              <th class="col-xs-2 text-center">Taux d'extraction<br/><small>(kg/hl)</small></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($form['produits'] as $produit): ?>
              <tr class="vertical-center">
                <td styl><?php echo $produit['taux_extraction']->renderLabel(null, array('style' => 'font-weight: normal')) ?></td>
                <td><?php echo $produit['taux_extraction']->render(['class' => 'form-control text-right input-float input-sm']) ?></td>
              </tr>
            <?php endforeach ?>
          </tbody>
        </table>
        </form>
      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-xs-6 text-left">
            <a href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-default">Annuler</a>
          </div>
          <div class="col-xs-6">
            <button form="form_extraction" type="submit" class="btn btn-success">Enregistrer</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal-backdrop fade in"></div>
<script>
  document.querySelector('body').classList.add('modal-open');
</script>
