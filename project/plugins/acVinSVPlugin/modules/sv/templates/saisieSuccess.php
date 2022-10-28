<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_SAISIE)); ?>

<h3>Saisie des données de production <?php if($cvi): ?>de <?php echo $cvi ?><?php endif; ?></h3>
<form action="" method="POST">
  <?php echo $form->renderHiddenFields(); ?>
  <?php echo $form->renderGlobalErrors(); ?>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th class="col-xs-3">Apporteur</th>
        <th class="col-xs-3">Produit</th>
        <th class="col-xs-1 text-center">Superficie récolté<br /><small>(ares)</small></th>
        <?php if($type != "SV11"): ?>
        <th class="col-xs-1 text-center">Quantité récolté<br /><small>(kg)</small></th>
        <th class="col-xs-1 text-center">Coefficient</th>
        <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
        <?php else: ?>
        <th class="col-xs-1 text-center">Volume récolté<br /><small>(hl)</small></th>
        <th class="col-xs-1 text-center">Volume revendiqué<br /><small>(hl)</small></th>
        <th class="col-xs-1 text-center">Usage industriels<br /><small>(hl)</small></th>
        <th class="col-xs-1 text-center">VCI<br /><small>(hl)</small></th>
        <?php endif; ?>
      </tr>
    </thead>
  <?php foreach($form['produits'] as $hash => $formProduit): ?>
  <?php $produit = $sv->get($hash); ?>
    <tr>
      <td><?php echo $produit->nom ?><br /><small class="text-muted"><?php echo $produit->cvi ?> - <?php echo $produit->commune ?></small></td>
    <td><?php echo $produit->libelle ?></td>
    <td><?php echo $formProduit['superficie_recolte']->render() ?></td>
    <?php if($type != "SV11"): ?>
    <td><?php echo $formProduit['quantite_recolte']->render() ?></td>
    <td><?php echo $formProduit['coefficient']->render() ?></td>
    <td><?php echo $formProduit['volume_revendique']->render() ?></td>
    <?php else: ?>
      <td><?php echo $formProduit['volume_recolte']->render() ?></td>
      <td><?php echo $formProduit['volume_revendique']->render() ?></td>
      <td><?php echo $formProduit['usages_industriels']->render() ?></td>
      <td><?php echo $formProduit['vci']->render() ?></td>
    <?php endif; ?>
  </tr>
  <?php endforeach; ?>
  </table>

  <div class="row">
    <div class="col-xs-6 text-left"><a href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
    <div class="col-xs-6 text-right"><button type="submit" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></button></div>
  </div>
</form>
<script>
  let calculVolumeRenvendique = function(ligne) {
    let input_quantite = ligne.querySelector('.input_quantite');
    let input_coefficient = ligne.querySelector('.input_coefficient');
    let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

    if(!input_coefficient.value) {
      return;
    }

    if(!input_quantite.value) {
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
    });
    item.addEventListener('change', function(e) {
      calculVolumeRenvendique(this.parentNode.parentNode);
    });
  });
  document.querySelectorAll('.input_coefficient').forEach(function(item) {
    item.addEventListener('keyup', function(e) {
      calculVolumeRenvendique(this.parentNode.parentNode);
    });
    item.addEventListener('change', function(e) {
      calculVolumeRenvendique(this.parentNode.parentNode);
    });
  });
  document.querySelectorAll('.input_volume_revendique').forEach(function(item) {
    item.addEventListener('change', function(e) {
      calculQuantite(this.parentNode.parentNode);
      calculCoefficient(this.parentNode.parentNode);
    });
  });
</script>