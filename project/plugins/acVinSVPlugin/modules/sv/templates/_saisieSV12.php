<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-4">Apporteur</th>
      <th class="col-xs-4">Produit</th>
      <th class="col-xs-1 text-center">Superficie déclarée<br /><small>(ares)</small></th>
      <th class="col-xs-1 text-center">Quantité récolté<br /><small>(kg)</small></th>
      <th class="col-xs-1 text-center">Taux d'extraction<br /><small>(kg/hl)</small></th>
      <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($form['produits'] as $hash => $formProduit): ?>
  <?php $produit = $sv->get($hash); ?>
    <tr>
      <td><?php echo $produit->nom ?><br /><small class="text-muted"><?php echo $produit->cvi ?> - <?php echo $produit->commune ?></small></td>
      <td><?php echo $produit->getRawValue()->getLibelleHtml() ?></td>
      <td><?php echo $formProduit['superficie_recolte']->render() ?></td>
      <td><?php echo $formProduit['quantite_recolte']->render() ?></td>
      <td><?php echo $formProduit['taux_extraction']->render() ?></td>
      <td><?php echo $formProduit['volume_revendique']->render() ?></td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>

<?php if ($form->hasMouts()): ?>
<h3>Apport de Moûts</h3>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-4">Apporteur</th>
      <th class="col-xs-4">Produit</th>
      <th class="col-xs-1 text-center">Volume de moûts<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Volume de moûts revendiqué<br /><small>(hl)</small></th>
      <th class="col-xs-2 text-center"></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($form['produits'] as $hash => $formProduit): ?>
  <?php if(!isset($formProduit['volume_mouts'])): continue; endif; ?>
  <?php $produit = $sv->get($hash); ?>
    <tr>
      <td><?php echo $produit->nom ?><br /><small class="text-muted"><?php echo $produit->cvi ?> - <?php echo $produit->commune ?></small></td>
      <td><?php echo $produit->getRawValue()->getLibelleHtml() ?></td>
      <td><?php echo $formProduit['volume_mouts']->render() ?></td>
      <td><?php echo $formProduit['volume_mouts_revendique']->render() ?></td>
      <th></th>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>

<?php endif ?>

