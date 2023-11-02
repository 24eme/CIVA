<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_EXTRACTION)); ?>

<h3>Extraction du volume à revendiquer</h3>

<p style="margin-bottom: 15px;">Saisissez ici le volume obtenu réels, afin d'obtenir le taux d'extraction, le  volume revendiqué par apporteur sera automatiquement calculé par rapport à ce taux.</p>

<form action="<?php echo url_for('sv_extraction', ['sf_subject' => $sv]) ?>" method="POST" id="form_extraction">
<?php echo $form->renderHiddenFields() ?>
<?php echo $form->renderGlobalErrors() ?>
<table class="table table-bordered table-striped table-condensed" style="margin-bottom: 0;">
  <thead>
    <tr>
      <th class="col-xs-6">Produit</th>
      <th class="col-xs-2 text-center">Quantité récolté<br/><small>(kg)</small></th>
      <th class="col-xs-2 text-center">Volume extrait<br/><small>(hl)</small></th>
      <th class="col-xs-2 text-center">Taux d'extraction<br/><small>(kg/hl)</small></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($form['produits'] as $hash => $produitForm): ?>
      <?php $produit = $sv->extraction->get($hash); ?>
      <tr class="vertical-center">
        <td><?php echo $produit->getRawValue()->getLibelleHtml() ?></td>
        <td class="text-right"><span class="span_quantite_recolte"><?php echo $produit->getQuantiteRecolte() ?></span> <small class="text-muted">kg</small></td>
        <td><?php echo $produitForm['volume_extrait']->render(['class' => 'form-control text-right input-float input-sm input_volume_extrait']) ?></td>
        <td><?php echo $produitForm['taux_extraction']->render(['class' => 'form-control text-right input-float input-sm input_taux_extraction']) ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>
</form>

<script>
document.querySelectorAll('.input_volume_extrait').forEach(function(item) {
  item.addEventListener('change', function(e) {
    calcul(this.closest('tr'));
  });
  item.addEventListener('keyup', function(e) {
    calcul(this.closest('tr'), e.target);
  });
  item.addEventListener('blur', function(e) {
    calcul(this.closest('tr'));
  });
});
let calcul = function(ligne, e) {
  let input_taux_extraction = ligne.querySelector('.input_taux_extraction');
  let input_volume_extrait = ligne.querySelector('.input_volume_extrait');
  let span_quantite_recolte = ligne.querySelector('.span_quantite_recolte');

  if(!parseFloat(input_volume_extrait.value)) {
    input_taux_extraction.value = "";

    return;
  }

  input_taux_extraction.value = Math.round(parseFloat(span_quantite_recolte.innerHTML) / parseFloat(input_volume_extrait.value)*100)/100;
}
</script>

<div class="row" style="margin-top: 30px;">
  <div class="col-xs-6 text-left"><a tabindex="-1" href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
  <div class="col-xs-6 text-right">
    <button type="submit" form="form_extraction" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></button>
  </div>
</div>
