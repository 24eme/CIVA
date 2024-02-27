<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-1 text-center">Superficie déclarée<br /><small>(ares)</small></th>
      <th class="col-xs-1 text-center">Quantité récolté<br /><small>(kg)</small></th>
      <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
      <?php if ($sv->hasMouts()): ?>
        <th class="col-xs-1 text-center">Superficie de moûts<br /><small>(ares)</small></th>
        <th class="col-xs-1 text-center">Volume de moûts<br /><small>(hl)</small></th>
      <?php endif ?>
      <th class="col-xs-1 text-center">Nombre d'apporteurs</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($sv->getRecapProduits() as $hash => $produit): ?>
    <?php if (! $produit->superficie_recolte && ! $produit->quantite_recolte && ! $produit->volume_revendique && !$produit->superficie_mouts && !$produit->volume_mouts): ?>
      <?php continue; ?>
    <?php endif ?>
    <tr>
      <td><?php echo $produit->getRawValue()->libelle_html ?></td>
      <td class="text-right"><?php echoFloat($produit->superficie_recolte) ?> <small class="text-muted">ares</small></td>
      <td class="text-right"><?php echo $produit->quantite_recolte ?> <small class="text-muted">kg</small></td>
      <td class="text-right"><?php echoFloat($produit->volume_revendique) ?> <small class="text-muted">hl</small></td>
      <?php if ($sv->hasMouts()): ?>
        <td class="text-right"><?php if($produit->superficie_mouts > 0): ?><?php echoFloat($produit->superficie_mouts) ?> <small class="text-muted">ares</small><?php endif; ?></td>
        <td class="text-right"><?php if($produit->volume_mouts > 0): ?><?php echoFloat($produit->volume_mouts) ?> <small class="text-muted">hl</small><?php endif; ?></td>
      <?php endif ?>
      <td class="text-right"><?php echo count($produit->apporteurs) ?></td>
    </tr>
  <?php endforeach ?>
  <tr>
    <?php $totalParColonne = $sv->getSum() ?>
    <td class="text-right"><strong>Total</strong></td>
    <td class="text-right"><?php echoFloat($totalParColonne['superficie_recolte']) ?> <small class="text-muted">ares</small></td>
    <td class="text-right"><?php echo $totalParColonne['recolte'] ?> <small class="text-muted">kg</small></td>
    <td class="text-right"><?php echoFloat($totalParColonne['revendique']) ?> <small class="text-muted">hl</small></td>
    <?php if ($sv->hasMouts()): ?>
      <td class="text-right"><?php echoFloat($totalParColonne['superficie_mouts']) ?> <small class="text-muted">ares</small></td>
      <td class="text-right"><?php echoFloat($totalParColonne['mouts']) ?> <small class="text-muted">hl</small></td>
    <?php endif ?>
    <td class="text-right"><?php echo count($sv->apporteurs) ?></td>
  </tr>
  </tbody>
</table>

