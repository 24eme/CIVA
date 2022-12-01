<?php use_helper('Float'); ?>
<?php include_partial('sv/step', ['object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_LIEU_STOCKAGE]) ?>

<h3>Lieu de stockage</h3>

<form action="" method="POST">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th class="col-xs-3">Produit</th>
        <?php foreach($sv->stockage as $stockage): ?>
        <th class="col-xs-1 text-center"><?php echo $stockage->numero ?></th>
        <?php endforeach; ?>
        <th class="col-xs-1 text-center">Total</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($form['produits'] as $hash => $formProduit): ?>
      <?php $produit = $recapProduits[$hash]; ?>
      <tr>
        <td><?php echo $produit->getRawValue()->libelle_html ?></td>
        <?php foreach($formProduit as $num_stockage => $formStockage): ?>
          <td class="col-xs-1"><?php echo $formStockage->render() ?></td>
        <?php endforeach ?>
        <td class="col-xs-1 text-right"><?php echoFloat($produit->volume_revendique) ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <div class="row">
    <div class="col-xs-6">
      <a href="<?php echo url_for('sv_autres', $sv) ?>" class="btn btn-default">
        <span class="glyphicon glyphicon-chevron-left"></span> Étape précédente
      </a>
    </div>
    <div class="col-xs-6 text-right">
      <a href="<?php echo url_for('sv_validation', $sv) ?>" class="btn btn-success">
        <span class="glyphicon glyphicon-chevron-right"></span> Étape suivante
      </a>
    </div>
  </div>
</form>