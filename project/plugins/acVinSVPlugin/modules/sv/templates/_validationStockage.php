<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th class="col-xs-3">Produit</th>
        <?php foreach($sv->getNotEmptyLieuxStockage() as $stockage): ?>
          <th class="col-xs-1 text-center">
            <?php echo $stockage->numero ?><br /><?php echo $stockage->nom ?><br /><?php echo $stockage->adresse ?><br  /><?php echo $stockage->code_postal ?> <?php echo $stockage->commune ?>
          </th>
        <?php endforeach; ?>
        <th class="col-xs-1 text-center">Total revendiqué</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($sv->getRecapProduits() as $hash => $produit): ?>
      <tr class="vertical-center">
        <td><?php echo $produit->getRawValue()->libelle_html ?></td>
        <?php foreach($sv->getNotEmptyLieuxStockage() as $id => $lieu): ?>
          <?php $produits = $lieu->produits->getRawValue(); ?>
          <?php if (is_array($produits) === false) { $produits = $produits->toArray(); } ?>
          <td class="col-xs-1 text-right" style="vertical-align: middle">
            <?php if (array_key_exists($hash, $produits)): ?>
              <?php echoFloat($produits[$hash]) ?> <span class="text-muted">hl</span>
            <?php endif ?>
          </td>
        <?php endforeach ?>
        <th class="col-xs-1 text-right"><span class="total"><?php echoFloat($produit->volume_revendique + $produit->volume_mouts_revendique) ?></span> <small class="text-muted">hl</span></th>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>
</div>
