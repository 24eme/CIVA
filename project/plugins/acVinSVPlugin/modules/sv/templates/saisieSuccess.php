<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_APPORTEURS)); ?>

<?php if($sv->getType() === SVClient::TYPE_SV12): ?>
<button type="submit" form="form_saisie" name="parametrage_extraction" value="1" class="pull-right btn btn-link btn-sm"><span class="glyphicon glyphicon-cog"></span> Paramètrer les taux d'extraction globaux</button>
<?php endif; ?>

<h3>Saisie des données de production <?php if($cvi): ?>de <?php echo EtablissementClient::getInstance()->find($cvi)->raison_sociale ?> (<?php echo $cvi ?>)<?php endif; ?></h3>

<p>Texte d'intro</p>

<form id="form_saisie" action="" method="POST">
  <?php echo $form->renderHiddenFields(); ?>
  <?php echo $form->renderGlobalErrors(); ?>

  <?php if($sv->getType() === SVClient::TYPE_SV11): ?>
    <?php include_partial('sv/saisieSV11', ['form' => $form, 'sv' => $sv, 'cvi' => $cvi]) ?>
  <?php else: ?>
    <?php include_partial('sv/saisieSV12', ['form' => $form, 'sv' => $sv, 'cvi' => $cvi]) ?>
  <?php endif ?>

  <div class="row">
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

<?php if(isset($showModalExtraction) && $showModalExtraction): ?>
  <?php include_component('sv', 'modalExtraction', array('sv' => $sv, 'url' => url_for('sv_saisie', ['sf_subject' => $sv, 'cvi' => $cvi]))); ?>
<?php endif; ?>
