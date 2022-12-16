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
    <td class="text-right"><?php if($produit->volume_detruit): ?><?php echoFloat($produit->volume_detruit) ?> <small class="text-muted">hl</small><?php endif; ?></td>
    <td class="text-right"><?php if($produit->vci): ?><?php echoFloat($produit->vci) ?> <small class="text-muted">hl</small><?php endif; ?></td>
    <td class="text-right"><?php echo count($produit->apporteurs) ?></td>
  </tr>
  <?php endforeach ?>
  <tr>
    <?php $totalParColonne = $sv->getSum() ?>
    <td class="text-right"><strong>Total</strong></td>
    <td class="text-right"><?php echoFloat($totalParColonne['superficie']) ?> <small class="text-muted">ares</small></td>
    <td class="text-right"><?php echoFloat($totalParColonne['recolte']) ?> <small class="text-muted">hl</small></td>
    <td class="text-right"><?php echoFloat($totalParColonne['revendique']) ?> <small class="text-muted">hl</small></td>
    <td class="text-right"><?php echoFloat($totalParColonne['volume_detruit']) ?> <small class="text-muted">hl</small></td>
    <td class="text-right"><?php echoFloat($totalParColonne['vci']) ?> <small class="text-muted">hl</small></td>
    <td class="text-right"><?php echo count($sv->apporteurs) ?></td>
  </tr>
  </tbody>
</table>
