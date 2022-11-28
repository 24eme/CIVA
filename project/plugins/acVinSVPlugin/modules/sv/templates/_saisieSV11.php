<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-3">Apporteur</th>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-1 text-center">Superficie déclarée<br /><small>(ares)</small></th>
      <th class="col-xs-1 text-center">Volume récolté<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Volume à détruire<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">VCI<br /><small>(hl)</small></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($form['produits'] as $hash => $formProduit): ?>
  <?php $produit = $sv->get($hash); ?>
  <tr>
    <td><?php echo $produit->nom ?><br /><small class="text-muted"><?php echo $produit->cvi ?> - <?php echo $produit->commune ?></small></td>
    <td><?php echo $produit->libelle ?></td>
    <td><?php echo $formProduit['superficie_recolte']->render() ?></td>
    <td><?php echo $formProduit['volume_recolte']->render() ?></td>
    <td><?php echo $formProduit['volume_revendique']->render() ?></td>
    <td><?php echo $formProduit['usages_industriels']->render() ?></td>
    <td><?php echo $formProduit['vci']->render() ?></td>
  </tr>
  <?php endforeach ?>
  </tbody>
</table>
