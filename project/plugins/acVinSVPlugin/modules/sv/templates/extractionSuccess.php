<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_EXTRACTION)); ?>

<h3>Paramétrage des taux d'extraction réels</h3>

<p style="margin-bottom: 15px;">Saisissez ici vos taux d'extraction réels, la conversion se fera alors automatiquemnt pour TOUS vos apports.</p>

<form action="<?php echo url_for('sv_extraction', ['sf_subject' => $sv]) ?>" method="POST" id="form_extraction">
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
        <td><?php echo $produit['taux_extraction']->renderLabelName() ?></td>
        <td><?php echo $produit['taux_extraction']->render(['class' => 'form-control text-right input-float input-sm']) ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
</form>

<div class="row" style="margin-top: 30px;">
  <div class="col-xs-6 text-left"><a tabindex="-1" href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
  <div class="col-xs-6 text-right">
    <button type="submit" form="form_extraction" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></button>
  </div>
</div>
