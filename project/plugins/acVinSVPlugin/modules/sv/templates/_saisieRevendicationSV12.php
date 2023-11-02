<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-2">Apporteur</th>
      <th class="col-xs-3">Produit</th>
      <th class="col-xs-2 text-center">Superficie déclarée<br /><small>(ares)</small></th>
      <th class="col-xs-2 text-center">Quantité récolté<br /><small>(kg)</small></th>
      <th class="col-xs-2 text-center">Volume revendiqué<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center">Taux d'extraction<br /><small>(kg/hl)</small></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($form['produits'] as $hash => $formProduit): ?>
  <?php $produit = $sv->get($hash); ?>
    <tr class="vertical-center">
      <td><?php echo $produit->nom ?></td>
      <td><?php echo $produit->getRawValue()->getLibelleHtml() ?></td>
      <td class="text-right"><?php echoFloat($produit->superficie_recolte) ?> <small class="text-muted">ares</small></td>
      <td class="text-right"><?php echoFloat($produit->quantite_recolte) ?> <small class="text-muted">kg</small></td>
      <td><div class="input-group"><?php echo $formProduit['volume_revendique']->render() ?><input class="form-control text-right input-float input-sm input_volume_revendique_pre hidden" type="text" autocomplete="off" data-decimal-auto="2" data-decimal="2" readonly="readonly" /><span class="input-group-addon" style="background: #f2f2f2;"><small class="text-muted">hl</small></span></div></td>
      <td class="text-center"><?php echoFloat($produit->getTauxExtraction()) ?></td>
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
      <th class="col-xs-2 text-center">Volume de moûts revendiqué<br /><small>(hl)</small></th>
      <th class="col-xs-1 text-center"></th>
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
      <td></td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
<?php endif ?>

<script>
let preCalcul = function(ligne, inputPress) {
  let input_quantite_pre = ligne.querySelector('.input_quantite_pre');
  let input_quantite = ligne.querySelector('.input_quantite');
  let input_taux_extraction = ligne.querySelector('.input_taux_extraction');
  let taux_extraction = parseFloat(ligne.querySelector('.input_taux_extraction').dataset.config);
  let input_volume_revendique_pre = ligne.querySelector('.input_volume_revendique_pre');
  let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

  if(input_quantite.value && taux_extraction && !input_volume_revendique.value && inputPress.value) {
    input_taux_extraction.classList.add('strong');
    input_volume_revendique.classList.add('hidden');
    input_volume_revendique_pre.classList.remove('hidden');
    input_volume_revendique_pre.value = Math.round(parseFloat(input_quantite.value) / taux_extraction*100)/100;
    input_volume_revendique_pre.dispatchEvent(new Event('blur'));
  }

  if(input_volume_revendique.value && taux_extraction && !input_quantite.value && inputPress.value) {
    input_taux_extraction.classList.add('strong');
    input_quantite.classList.add('hidden');
    input_quantite_pre.classList.remove('hidden');
    input_quantite_pre.value = Math.round(parseFloat(input_volume_revendique.value) * parseFloat(taux_extraction));
    input_quantite_pre.dispatchEvent(new Event('blur'));
  }

  calculTauxExtraction(ligne);
}
let calcul = function(ligne, e) {
  let input_quantite_pre = ligne.querySelector('.input_quantite_pre');
  let input_quantite = ligne.querySelector('.input_quantite');
  let input_taux_extraction = ligne.querySelector('.input_taux_extraction');
  let input_volume_revendique_pre = ligne.querySelector('.input_volume_revendique_pre');
  let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

  input_taux_extraction.classList.remove('strong');

  if(input_volume_revendique_pre.value) {
    input_volume_revendique.value = input_volume_revendique_pre.value;
    input_volume_revendique_pre.value = null;
    setTimeout(function() {
      input_volume_revendique.classList.remove('hidden');
      input_volume_revendique_pre.classList.add('hidden');
    }, 100);
  }

  if(input_quantite_pre.value) {
    input_quantite.value = input_quantite_pre.value;
    input_quantite_pre.value = null;
    setTimeout(function() {
      input_quantite.classList.remove('hidden');
      input_quantite.classList.add('hidden');
    }, 100);
  }

  calculTauxExtraction(ligne);
}
let calculTauxExtraction = function(ligne) {
  let input_quantite = ligne.querySelector('.input_quantite');
  let input_taux_extraction = ligne.querySelector('.input_taux_extraction');
  let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

  if(parseFloat(input_volume_revendique.value) > 0 && parseFloat(input_quantite.value) > 0) {
    ligne.querySelector('.lien_parametrage').classList.add('hidden');
    input_taux_extraction.value = Math.round(parseFloat(input_quantite.value) / parseFloat(input_volume_revendique.value)*100)/100;
  } else if(parseFloat(input_taux_extraction.dataset.config)) {
    input_taux_extraction.value = input_taux_extraction.dataset.config;
  } else {
    input_taux_extraction.value = null;
    ligne.querySelector('.lien_parametrage').classList.remove('hidden');
  }
  input_taux_extraction.dispatchEvent(new Event('blur'));
}

document.querySelectorAll('.input_quantite').forEach(function(item) {
  item.addEventListener('change', function(e) {
    calcul(this.closest('tr'));
  });
  item.addEventListener('keyup', function(e) {
    preCalcul(this.closest('tr'), e.target);
  });
  item.addEventListener('blur', function(e) {
    calcul(this.closest('tr'));
  });
});
document.querySelectorAll('.input_volume_revendique').forEach(function(item) {
  item.addEventListener('change', function(e) {
    calcul(this.closest('tr'));
  });
  item.addEventListener('keyup', function(e) {
    preCalcul(this.closest('tr'), e.target);
  });
  item.addEventListener('blur', function(e) {
    calcul(this.closest('tr'));
  });
});
document.querySelectorAll('.input_recolte_pre').forEach(function(item) {
  item.addEventListener('focus', function(e) {
    let input_recolte = this.closest('tr').querySelector('.input_recolte');
    input_recolte.classList.remove('hidden');
    input_recolte.focus();
    this.classList.add('hidden');
  });
})
document.querySelectorAll('.input_volume_revendique_pre').forEach(function(item) {
  item.addEventListener('focus', function(e) {
    let input_volume_revendique = this.closest('tr').querySelector('.input_volume_revendique');
    input_volume_revendique.classList.remove('hidden');
    input_volume_revendique.focus();
    this.classList.add('hidden');
  });
})

</script>
