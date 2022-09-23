<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_VALIDATION)); ?>

<h3>Récapitulatif par produit</h3>
<table class="table table-bordered table-striped table-condensed">
  <thead>
    <tr>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-1 text-center">Superficie récolté<br /><small>(ares)</small></th>
      <th class="col-xs-1 text-center">Quantité récolté<br /><small>(kg)</small></th>
      <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Nombre d'apporteurs<br  />&nbsp;</th>
    </tr>
  </thead>
<?php foreach($sv->getRecapProduits() as $hash => $produit): ?>
<tr>
  <td><?php echo $produit->libelle ?></td>
  <td class="text-right"><?php echoFloat($produit->superficie_recolte) ?> <small class="text-muted">ares</small></td>
  <td class="text-right"><?php echo $produit->quantite_recolte ?> <small class="text-muted">kg</small></td>
  <td class="text-right"><?php echoFloat($produit->volume_revendique) ?> <small class="text-muted">hl</small></td>
  <td class="text-right"><?php echo count($produit->apporteurs) ?></td>
</tr>
<?php endforeach; ?>
</table>

<div class="row">
  <div class="col-xs-6 text-left"><a href="<?php echo url_for('sv_saisie', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
  <div class="col-xs-6 text-right"><button type="submit" class="btn btn-success">Terminer la saisie <span class="glyphicon glyphicon glyphicon-ok"></span></button></div>
</div>