<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-1 text-center">Superficie récolté<br /><small>(ares)</small></th>
      <th class="col-xs-1 text-center">Quantité récolté<br /><small>(kg)</small></th>
      <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Nombre d'apporteurs<br />&nbsp;</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($sv->getRecapProduits() as $hash => $produit): ?>
    <tr>
      <td><?php echo $produit->libelle ?></td>
      <td class="text-right"><?php echoFloat($produit->superficie_recolte) ?> <small class="text-muted">ares</small></td>
      <td class="text-right"><?php echo $produit->quantite_recolte ?> <small class="text-muted">kg</small></td>
      <td class="text-right"><?php echoFloat($produit->volume_revendique) ?> <small class="text-muted">hl</small></td>
      <td class="text-right"><?php echo count($produit->apporteurs) ?></td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>

