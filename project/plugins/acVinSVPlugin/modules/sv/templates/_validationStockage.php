<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th class="col-xs-3">Produit</th>
        <th class="col-xs-1 text-center">Total revendiqué</th>
        <?php foreach($sv->getNotEmptyLieuxStockage() as $stockage): ?>
          <th class="col-xs-1 text-center">
            <?php echo $stockage->numero ?><br /><?php echo $stockage->nom ?><br /><?php echo $stockage->adresse ?><br  /><?php echo $stockage->code_postal ?> <?php echo $stockage->commune ?>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
    <?php foreach($sv->getRecapProduits() as $hash => $produit): ?>
      <tr>
        <td style="vertical-align: middle"><?php echo $produit->getRawValue()->libelle_html ?></td>
        <th style="vertical-align: middle" class="col-xs-1 text-right"><span class="total"><?php echoFloat($produit->volume_revendique) ?></span> <small class="text-muted">hl</span></th>
        <?php foreach($sv->getNotEmptyLieuxStockage() as $id => $lieu): ?>
          <td class="col-xs-1 text-right" style="vertical-align: middle">
            <?php if (array_key_exists($hash, $lieu->produits->getRawValue())): ?>
              <?php echo $lieu->produits->get($hash) ?><span class="text-muted">hl</span>
            <?php endif ?>
          </td>
        <?php endforeach ?>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>
</div>
