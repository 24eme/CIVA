<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-1 text-center">Superficie déclarée<br /><small>(ares)</small></th>
      <th class="col-xs-1 text-center">Quantité récolté<br /><small>(kg)</small></th>
      <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Volume de moûts<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Nombre d'apporteurs</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($sv->getRecapProduits() as $hash => $produit): ?>
    <tr>
      <td><?php echo $produit->getRawValue()->libelle_html ?></td>
      <td class="text-right"><?php echoFloat($produit->superficie_recolte) ?> <small class="text-muted">ares</small></td>
      <td class="text-right"><?php echo $produit->quantite_recolte ?> <small class="text-muted">kg</small></td>
      <td class="text-right"><?php echoFloat($produit->volume_revendique) ?> <small class="text-muted">hl</small></td>
      <td class="text-right"><?php if($produit->volume_mouts > 0): ?><?php echoFloat($produit->volume_mouts) ?> <small class="text-muted">hl</small><?php endif; ?></td>
      <td class="text-right"><?php echo count($produit->apporteurs) ?></td>
    </tr>
  <?php endforeach ?>
  <tr>
    <?php $totalParColonne = $sv->getSum() ?>
    <td class="text-right"><strong>Total</strong></td>
    <td class="text-right"><strong><?php echoFloat($totalParColonne['superficie']) ?></strong> <small class="text-muted">ares</small></td>
    <td class="text-right"><strong><?php echo $totalParColonne['recolte'] ?></strong> <small class="text-muted">kg</small></td>
    <td class="text-right"><strong><?php echoFloat($totalParColonne['revendique']) ?></strong> <small class="text-muted">hl</small></td>
    <td class="text-right"><strong><?php echoFloat($totalParColonne['mouts']) ?></strong> <small class="text-muted">hl</small></td>
    <td class="text-right"><strong><?php echo count($sv->apporteurs) ?></strong></td>
  </tr>
  </tbody>
</table>

