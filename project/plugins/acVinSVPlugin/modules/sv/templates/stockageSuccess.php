<?php include_partial('sv/step', ['object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_LIEU_STOCKAGE]) ?>

<h3>Lieu de stockage</h3>

<div class="alert alert-warning" role="alert">
  <strong><i class="glyphicon glyphicon-warning-sign"></i> Attention</strong>
  <br/>
  Si vous avez plusieurs lieux de stockage, merci de contacter le CIVA afin de leur communiquer votre répartition.
</div>

<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-3">Produit</th>
      <?php foreach($sv->getEtablissementObject()->lieux_stockage as $lieu): ?>
      <th class="col-xs-1 text-center"><?php echo $lieu->numero ?><br /><?php echo $lieu->adresse ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
  <?php foreach($sv->getRecapProduits() as $hash => $produit): ?>
    <tr>
      <td><?php echo $produit->getRawValue()->libelle_html ?></td>
      <?php foreach($sv->getEtablissementObject()->lieux_stockage as $lieu): ?>
      <td class="col-xs-1"><input class="form-control text-right input-float input-sm" placeholder="hl" type="text" value="<?php echo $produit->volume_revendique ?>" autocomplete="off" data-decimal-auto="2" data-decimal="2"></th>
        <?php $produit->volume_revendique = null; ?>
      <?php endforeach; ?>
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
