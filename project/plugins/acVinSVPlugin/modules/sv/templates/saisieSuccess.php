<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_SAISIE)); ?>

<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-3">Apporteur</th>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-1 text-center">Superficie récolté<br /><small>(ares)</small></th>
      <th class="col-xs-1 text-center">Quantité récolté<br /><small>(kg)</small></th>
      <th class="col-xs-1 text-center">Coefficient</th>
      <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
    </tr>
  </thead>
<?php foreach($form['produits'] as $hash => $formProduit): ?>
<?php $produit = $sv->get($hash); ?>
<tr>
    <td><?php echo $produit->nom ?><br /><small class="text-muted"><?php echo $produit->cvi ?> - <?php echo $produit->commune ?></small></td>
  <td><?php echo $produit->libelle ?></td>
  <td><?php echo $formProduit['superficie_recolte']->render() ?></td>
  <td><?php echo $formProduit['quantite_recolte']->render() ?></td>
  <td><?php echo $formProduit['coefficient']->render() ?></td>
  <td><?php echo $formProduit['volume_revendique']->render() ?></td>
</tr>
<?php endforeach; ?>
</table>
<script>
  let calculVolumeRenvendique = function(ligne) {
    let input_quantite = ligne.querySelector('.input_quantite');
    let input_coefficient = ligne.querySelector('.input_coefficient');
    let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

    if(!input_coefficient.value) {
      return;
    }

    input_volume_revendique.value = Math.round(parseFloat(input_quantite.value) / parseFloat(input_coefficient.value)*100)/100;
  }
  let calculCoefficient = function(ligne) {
    let input_quantite = ligne.querySelector('.input_quantite');
    let input_coefficient = ligne.querySelector('.input_coefficient');
    let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

    if(!input_quantite.value) {
      return;
    }

    input_coefficient.value = Math.round(parseFloat(input_quantite.value) / parseFloat(input_volume_revendique.value)*100)/100;
  }
  let calculQuantite = function(ligne) {
    let input_quantite = ligne.querySelector('.input_quantite');
    let input_coefficient = ligne.querySelector('.input_coefficient');
    let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

    if(input_quantite.value) {
      return;
    }
    input_quantite.value = Math.round(parseFloat(input_volume_revendique.value) * parseFloat(input_coefficient.value));
  }
  document.querySelectorAll('.input_quantite').forEach(function(item) {
    item.addEventListener('keyup', function(e) {
      calculVolumeRenvendique(this.parentNode.parentNode);
  })});
  document.querySelectorAll('.input_coefficient').forEach(function(item) {
    item.addEventListener('keyup', function(e) {
      calculVolumeRenvendique(this.parentNode.parentNode);
  })});
  document.querySelectorAll('.input_volume_revendique').forEach(function(item) {
    item.addEventListener('change', function(e) {
      calculQuantite(this.parentNode.parentNode);
    });
    item.addEventListener('keyup', function(e) {
      calculCoefficient(this.parentNode.parentNode);
    });
  });
</script>