<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-1 text-center">Superficie déclarée<br /><small>(ares)</small></th>
      <th class="col-xs-1 text-center">Volume récolté<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Volume à détruire<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">VCI<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Nombre d'apporteurs<br />&nbsp;</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($sv->getRecapProduits() as $hash => $produit): ?>
  <tr>
    <td><?php echo $produit->getRawValue()->libelle_html ?></td>
    <td class="text-right"><?php echoFloat($produit->superficie_recolte) ?> <small class="text-muted">ares</small></td>
    <td class="text-right"><?php echo $produit->volume_recolte ?> <small class="text-muted">hl</small></td>
    <td class="text-right"><?php echoFloat($produit->volume_revendique) ?> <small class="text-muted">hl</small></td>
    <td class="text-right"><?php echoFloat($produit->usages_industriels) ?> <small class="text-muted">hl</small></td>
    <td class="text-right"><?php echoFloat($produit->vci) ?> <small class="text-muted">hl</small></td>
    <td class="text-right"><?php echo count($produit->apporteurs) ?></td>
  </tr>
  <?php endforeach ?>
  </tbody>
</table>
