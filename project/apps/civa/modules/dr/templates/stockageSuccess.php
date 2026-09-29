<?php use_helper('Float'); ?>
<?php use_stylesheet('/css/declaration_recolte.css', 'first') ?>
<?php include_partial('dr/etapes', array('etape' => 5, 'dr' => $dr)) ?>
<?php include_partial('dr/actions', array('etape' => 0, 'help_popup_action'=>$help_popup_action)) ?>

<ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#exploitation_autres">Lieux de stockage</a></li>
</ul>

<div id="application_dr" class="clearfix">

<p style="margin-bottom: 15px">D'après la nouvelle réglementation entrée en vigueur à compter de 2022, vous devez ici répartir les volumes produits entre vos différents lieux de stockage, à la date de dépôt de votre déclaration.</p>

<form action="" method="POST">
  <?php echo $form->renderHiddenFields(); ?>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th class="col-xs-3">Produit</th>
        <th class="col-xs-1 text-center">Total revendiqué</th>
        <?php foreach($dr->stockage as $stockage): ?>
        <th class="text-center" style="word-break: break-word"><?php echo $stockage->adresse ?><br  /><?php echo $stockage->code_postal ?> <?php echo $stockage->commune ?><br/><small><?php echo $stockage->numero ?><br /><?php echo $stockage->nom ?></small></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
    <?php foreach($form['produits'] as $hash => $formProduit): ?>
      <?php $produit = $recapProduits[$hash]; ?>
      <tr>
        <td style="vertical-align: middle"><?php echo $produit->getRawValue()->libelle_html ?></td>
        <th style="vertical-align: middle" class="col-xs-1 text-right">
            <span class="total"><?php echoFloat($produit->volume_revendique) ?></span> <small>hl</small>
        </th>
        <?php foreach($formProduit as $num_stockage => $formStockage): ?>
          <td><div class="input-group"><?php echo $formStockage->render() ?><span class="input-group-addon" style="background: #f2f2f2;"><small class="text-muted">hl</small></span></div></td>
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
  </div>
  <?php include_partial('dr/boutons', array('display' => array('precedent','suivant'), 'dr' => $dr)) ?>

</form>
