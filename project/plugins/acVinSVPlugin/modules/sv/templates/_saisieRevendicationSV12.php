
<?php if ($form->hasRevendication()): ?>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-2">Apporteur</th>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-2 text-center">Superficie déclarée<br /><small>(ares)</small></th>
      <th class="col-xs-2 text-center">Quantité récolté<br /><small>(kg)</small></th>
      <th class="col-xs-2 text-center">Volume revendiqué<br /><small>(hl)</small></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($form['produits'] as $hash => $formProduit): ?>
  <?php if(!isset($formProduit['volume_revendique'])): continue; endif; ?>
  <?php $produit = $sv->get($hash); ?>
    <tr class="vertical-center">
      <td><?php echo $produit->nom ?></td>
      <td><?php echo $produit->getRawValue()->getLibelleHtml() ?></td>
      <td class="text-right"><?php if ($produit->getRawValue()->isRebeche() === false): ?><?php echoFloat($produit->superficie_recolte) ?> <small class="text-muted">ares</small><?php endif ?></td>
      <td class="text-right"><?php if ($produit->getRawValue()->isRebeche() === false): ?><?php echoFloat($produit->quantite_recolte) ?> <small class="text-muted">kg</small><?php endif ?></td>
      <td><div class="input-group"><?php echo $formProduit['volume_revendique']->render() ?><span class="input-group-addon" style="background: #f2f2f2;"><small class="text-muted">hl</small></span></div></td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
<?php endif ?>

<?php if ($form->hasMouts()): ?>
<h4>Apport de Moûts</h4>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-2">Apporteur</th>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-2 text-center">Superficie de moûts <small>(ares)</small></th>
      <th class="col-xs-2 text-center">Volume de moûts<br /><small>(hl)</small></th>
      <th class="col-xs-2 text-center">Volume de moûts revendiqué<br /><small>(hl)</small></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($form['produits'] as $hash => $formProduit): ?>
  <?php if(!isset($formProduit['volume_mouts_revendique'])): continue; endif; ?>
  <?php $produit = $sv->get($hash); ?>
    <tr class="vertical-center">
      <td><?php echo $produit->nom ?></td>
      <td><?php echo $produit->getRawValue()->getLibelleHtml() ?></td>
      <td class="text-right"><?php echoFloat($produit->superficie_mouts) ?> <small class="text-muted">ares</small></td>
      <td class="text-right"><?php echoFloat($produit->volume_mouts) ?> <small class="text-muted">hl</small></td>
      <td><div class="input-group"><?php echo $formProduit['volume_mouts_revendique']->render() ?><span class="input-group-addon" style="background: #f2f2f2;"><small class="text-muted">hl</small></span></div></td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
<?php endif ?>
