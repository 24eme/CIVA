<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_APPORTEURS)); ?>

<h3>Saisie des données de production <?php if($cvi): ?>de <?php echo EtablissementClient::getInstance()->find($cvi)->raison_sociale ?> (<?php echo $cvi ?>)<?php endif; ?></h3>

<p>Texte d'intro</p>

<form action="" method="POST">
  <?php echo $form->renderHiddenFields(); ?>
  <?php echo $form->renderGlobalErrors(); ?>

  <?php if($sv->getType() === SVClient::TYPE_SV11): ?>
    <?php include_partial('sv/saisieSV11', ['form' => $form, 'sv' => $sv]) ?>
  <?php else: ?>
    <?php include_partial('sv/saisieSV12', ['form' => $form, 'sv' => $sv]) ?>
  <?php endif ?>

  <div class="row">
    <?php if (isset($cvi_precedent)): ?>
      <div class="col-xs-4 text-left"><a href="<?php echo url_for('sv_saisie', ['id' => $sv->_id, 'cvi' => $cvi_precedent]) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Apporteur précédent</a></div>
    <?php else: ?>
      <div class="col-xs-4 text-left"><a href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Précédent</a></div>
    <?php endif ?>
    <div class="col-xs-4 text-center"><a href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-default">Voir la liste des apporteur</a></div>
    <div class="col-xs-4 text-right"><button type="submit" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></button></div>
  </div>
</form>

<?php if($sv->getType() == SVClient::TYPE_SV12): ?>
<script>
  let calculVolumeRenvendique = function(ligne) {
    let input_quantite = ligne.querySelector('.input_quantite');
    let input_taux_extraction = ligne.querySelector('.input_taux_extraction');
    let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

    if(!input_taux_extraction.value) {
      return;
    }

    if(!input_quantite.value) {
      return;
    }

    input_volume_revendique.value = Math.round(parseFloat(input_quantite.value) / parseFloat(input_taux_extraction.value)*100)/100;
  }
  let calculTauxExtraction = function(ligne) {
    let input_quantite = ligne.querySelector('.input_quantite');
    let input_taux_extraction = ligne.querySelector('.input_taux_extraction');
    let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

    if(!input_quantite.value) {
      return;
    }

    input_taux_extraction.value = Math.round(parseFloat(input_quantite.value) / parseFloat(input_volume_revendique.value)*100)/100;
  }
  let calculQuantite = function(ligne) {
    let input_quantite = ligne.querySelector('.input_quantite');
    let input_taux_extraction = ligne.querySelector('.input_taux_extraction');
    let input_volume_revendique = ligne.querySelector('.input_volume_revendique');

    if(input_quantite.value) {
      return;
    }
    input_quantite.value = Math.round(parseFloat(input_volume_revendique.value) * parseFloat(input_taux_extraction.value));
  }
  document.querySelectorAll('.input_quantite').forEach(function(item) {
    item.addEventListener('keyup', function(e) {
      calculVolumeRenvendique(this.parentNode.parentNode);
    });
    item.addEventListener('change', function(e) {
      calculVolumeRenvendique(this.parentNode.parentNode);
    });
  });
  document.querySelectorAll('.input_taux_extraction').forEach(function(item) {
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
      calculTauxExtraction(this.parentNode.parentNode);
    });
  });
</script>
<?php endif ?>
