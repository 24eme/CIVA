<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance($sv->type), 'step' => SVEtapes::ETAPE_REVENDICATION)); ?>

<h3><?php echo $sv->apporteurs->get($cvi)->getNom() ?> <small><?php echo $cvi ?> - <?php echo $sv->apporteurs->get($cvi)->getCommune(); ?></small></h3>

<p style="margin-bottom: 15px;">Saisissez ici les données de production de cet apporteur.</p>

<form id="form_saisie" action="" method="POST">
  <?php echo $form->renderHiddenFields(); ?>
  <?php echo $form->renderGlobalErrors(); ?>

  <?php include_partial('sv/saisieRevendicationSV12', ['form' => $form, 'sv' => $sv, 'cvi' => $cvi]) ?>

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
