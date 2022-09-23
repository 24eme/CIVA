<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_PRODUITS)); ?>

<h3>Coefficient de soutirage</h3>
<table class="table table-bordered table-striped table-condensed">
  <thead>
    <tr>
      <th class="col-xs-10">Produit</th>
      <th class="col-xs-2 text-center">Coefficient de soutirage</th>
    </tr>
  </thead>
<?php foreach($sv->getRecapProduits() as $hash => $produit): ?>
<tr>
  <td><?php echo $produit->libelle ?></td>
  <td class="text-right"><input type="text" value="<?php if(preg_match('/EFF/', $hash)): ?>150.00<?php else: ?>130.00<?php endif; ?>" class="form-control text-right input-float input-sm" /></td>
</tr>
<?php endforeach; ?>
</table>

<div class="row">
  <div class="col-xs-6 text-left"><a href="<?php echo url_for('sv_exploitation', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
  <div class="col-xs-6 text-right"><a href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></a></div>
</div>