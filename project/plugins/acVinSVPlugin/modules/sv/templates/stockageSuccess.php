<?php use_helper('Float'); ?>
<?php include_partial('sv/step', ['object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_LIEU_STOCKAGE]) ?>

<h3>Lieux de stockage</h3>

<p style="margin-bottom: 15px">D'après la nouvelle réglementation entrée en vigueur à compter de 2022, vous devez ici répartir les volumes produits entre vos différents lieux de stockage, à la date de dépôt de votre déclaration.</p>

<form action="" method="POST">
  <?php echo $form->renderHiddenFields(); ?>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th class="col-xs-3">Produit</th>
        <th class="col-xs-1 text-center">Total revendiqué</th>
        <?php foreach($sv->stockage as $stockage): ?>
        <th class="col-xs-1 text-center"><?php echo $stockage->numero ?><br /><?php echo $stockage->nom ?><br /><?php echo $stockage->adresse ?><br  /><?php echo $stockage->code_postal ?> <?php echo $stockage->commune ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
    <?php foreach($form['produits'] as $hash => $formProduit): ?>
      <?php $produit = $recapProduits[$hash]; ?>
      <tr>
        <td style="vertical-align: middle"><?php echo $produit->getRawValue()->libelle_html ?></td>
        <th style="vertical-align: middle" class="col-xs-1 text-right"><span class="total">
            <?php if (isset($produit->volume_revendique_mout) && $produit->volume_revendique): ?>
            (R+M) <?php echoFloat($produit->volume_revendique + $produit->volume_revendique_mout) ?></span> <small>hl</span>
            <?php elseif (isset($produit->volume_revendique_mout) && !$produit->volume_revendique): ?>
            (M) <?php echoFloat($produit->volume_revendique) ?></span> <small>hl</span>
            <?php else: ?>
            <?php echoFloat($produit->volume_revendique) ?></span> <small>hl</span>
            <?php endif; ?>
        </th>
        <?php foreach($formProduit as $num_stockage => $formStockage): ?>
          <td class="col-xs-1"><div class="input-group"><?php echo $formStockage->render() ?><span class="input-group-addon" style="background: #f2f2f2;"><small class="text-muted">hl</small></span></div></td>
        <?php endforeach ?>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <script>
    (document.querySelectorAll('input.secondaire') || []).forEach(function(item) {
      item.addEventListener('change', function(e) {
        let parent = this.parentNode;
        while (parent.tagName !== 'TR') {
          parent = parent.parentNode;

          if (parent === null) {
            console.error('Balise tr non trouvée');
            return false;
          }
        }

        const tr = parent;
        const inputPrincipal = tr.querySelector('.principal');
        const total = parseFloat(tr.querySelector('.total').innerText);
        let totalSecondaire = 0;
        tr.querySelectorAll('input.secondaire').forEach(function(inputSecondaire) {
          if(!inputSecondaire.value) {
            return;
          }
          totalSecondaire += parseFloat(inputSecondaire.value);
        });
        inputPrincipal.value = total - totalSecondaire;
        if(parseFloat(inputPrincipal.value) < 0) {
          inputPrincipal.value = 0;
          this.value = total - totalSecondaire + parseFloat(this.value);
        }
        inputPrincipal.dispatchEvent(new Event('change'));
      });
    })
  </script>

  <div class="row" style="margin-top: 30px;">
    <div class="col-xs-6">
      <a href="<?php echo url_for('sv_autres', $sv) ?>" class="btn btn-default">
        <span class="glyphicon glyphicon-chevron-left"></span> Étape précédente
      </a>
    </div>
    <div class="col-xs-6 text-right">
      <button type="submit" class="btn btn-success">
        <span class="glyphicon glyphicon-chevron-right"></span> Étape suivante
      </button>
    </div>
  </div>
</form>