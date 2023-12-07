<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance($sv->type), 'step' => SVEtapes::ETAPE_APPORTEURS)); ?>

<h3><?php echo $sv->apporteurs->get($cvi)->getNom() ?> <small><?php echo $cvi ?> - <?php echo $sv->apporteurs->get($cvi)->getCommune(); ?></small></h3>

<p style="margin-bottom: 15px;">Saisissez ici les données de production de cet apporteur.</p>

<form id="form_saisie" action="" method="POST">
  <?php echo $form->renderHiddenFields(); ?>
  <?php echo $form->renderGlobalErrors(); ?>

  <?php if($sv->getType() === SVClient::TYPE_SV11): ?>
    <?php include_partial('sv/saisieSV11', ['form' => $form, 'sv' => $sv, 'cvi' => $cvi]) ?>
  <?php else: ?>
    <?php include_partial('sv/saisieSV12', ['form' => $form, 'sv' => $sv, 'cvi' => $cvi]) ?>
  <?php endif ?>

  <div class="text-right"  style="margin-top: -5px;">
      <button name="ajout-produit" type="submit" tabindex="-1" class="btn btn-sm btn-default" value="1"><span class="glyphicon glyphicon-plus"></span> Ajouter un produit</button>
  </div>

  <div class="row" style="margin-top: 30px;">
    <?php if (isset($cvi_precedent)): ?>
      <div class="col-xs-4 text-left">
        <button type="submit" name="precedent_cvi" value="<?php echo $cvi_precedent ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Apporteur précédent</button>
      </div>
    <?php else: ?>
      <div class="col-xs-4 text-left"><button type="submit" name="retour_liste" value="1" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Précédent</button></div>
    <?php endif ?>
    <div class="col-xs-4 text-center"><button type="submit" name="retour_liste" value="1" class="btn btn-default"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Retour à la liste des apporteurs</button></div>
    <div class="col-xs-4 text-right"><button type="submit" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></button></div>
  </div>
</form>

<?php include_partial('sv/modalAjoutProduit', ['sv' => $sv, 'cvi' => $cvi, 'form' => $formAjoutProduit]); ?>

<?php if ($modal): ?>
<script>
  document.addEventListener('DOMContentLoaded', function (e) {
    $('#modal_ajout_produit').modal('show')
  });
</script>
<?php endif ?>
