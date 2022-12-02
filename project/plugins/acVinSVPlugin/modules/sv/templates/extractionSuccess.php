<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_EXTRACTION)); ?>

<h3>Taux d'extraction</h3>

<p>Texte d'intro</p>

<form action="" method="POST" id="form_extraction">
<?php echo $form->renderHiddenFields() ?>
<table class="table table-bordered table-striped table-condensed">
  <thead>
    <tr>
      <th class="col-xs-1 text-center">Taux d'extraction</th>
      <th class="col-xs-11">Produit</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($form['produits'] as $produit): ?>
      <tr>
        <td><?php echo $produit['taux_extraction']->render(['class' => 'form-control text-right input-float input-xs']) ?></td>
        <td><?php echo $produit['taux_extraction']->renderLabel(null, array('style' => 'font-weight: normal')) ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
</form>

<div class="row">
  <div class="col-xs-6 text-left"><a href="<?php echo url_for('mon_espace_civa_production', $sv->getEtablissementObject()) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
  <div class="col-xs-6 text-right">
    <button type="submit" form="form_extraction" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></button>
  </div>
</div>
