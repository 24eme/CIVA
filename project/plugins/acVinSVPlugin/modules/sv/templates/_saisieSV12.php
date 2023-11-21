<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-2">Apporteur</th>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-2 text-center">Superficie déclarée<br /><small>(ares)</small></th>
      <th class="col-xs-2 text-center">Quantité récolté<br /><small>(kg)</small></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($form['produits'] as $hash => $formProduit): ?>
  <?php $produit = $sv->get($hash); ?>
    <tr class="vertical-center">
      <td><?php echo $produit->nom ?></td>
      <td>
        <?php echo $produit->getRawValue()->getLibelleHtml() ?>
        <a href="<?php echo url_for('sv_suppression_produit_apporteur', ['id' => $sv->_id, 'hash' => str_replace('/', '-', $hash), 'cvi' => $produit->cvi]) ?>" class="strong pull-right text-danger" tabindex=-1>×</a>
      </td>
      <td><div class="input-group"><?php echo $formProduit['superficie_recolte']->render() ?><span class="input-group-addon" style="background: #f2f2f2;"><small class="text-muted">ares</small></span></div></td>
      <td><div class="input-group"><?php echo $formProduit['quantite_recolte']->render() ?><input class="form-control text-right input-float input-sm input_quantite_pre hidden" type="text" autocomplete="off" data-decimal-auto="2" data-decimal="2" readonly="readonly" /><span class="input-group-addon" style="background: #f2f2f2;"><small class="text-muted">kg</small></span></div></td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>

<?php if ($form->hasMouts()): ?>
<h4>Apport de Moûts</h4>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-2">Apporteur</th>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-2 text-center">Superficie de moûts <small>(ares)</small></th>
      <th class="col-xs-2 text-center">Volume de moûts<br /><small>(hl)</small></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($form['produits'] as $hash => $formProduit): ?>
  <?php if(!isset($formProduit['volume_mouts'])): continue; endif; ?>
  <?php $produit = $sv->get($hash); ?>
    <tr class="vertical-center">
      <td><?php echo $produit->nom ?></td>
      <td><?php echo $produit->getRawValue()->getLibelleHtml() ?></td>
      <td><div class="input-group"><?php echo $formProduit['superficie_mouts']->render() ?><span class="input-group-addon" style="background: #f2f2f2;"><small class="text-muted">ares</small></span></div></td>
      <td><div class="input-group"><?php echo $formProduit['volume_mouts']->render() ?><span class="input-group-addon" style="background: #f2f2f2;"><small class="text-muted">hl</small></span></div></td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
<?php endif ?>
