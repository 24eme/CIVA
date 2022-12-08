<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_APPORTEURS)); ?>

<?php if($sv->getType() === SVClient::TYPE_SV12): ?>
<a href="<?php echo url_for('sv_saisie', array('sf_subject' => $sv, 'cvi' => $cvi, 'parametrage_extraction' => 1)) ?>" class="pull-right btn btn-link btn-sm"><span class="glyphicon glyphicon-cog"></span> Paramètrer les taux d'extraction globaux</a>
<?php endif; ?>

<h3>Saisie des données de production <?php if($cvi): ?>de <?php echo EtablissementClient::getInstance()->find($cvi)->raison_sociale ?> (<?php echo $cvi ?>)<?php endif; ?></h3>

<p>Texte d'intro</p>

<form action="" method="POST">
  <?php echo $form->renderHiddenFields(); ?>
  <?php echo $form->renderGlobalErrors(); ?>

  <?php if($sv->getType() === SVClient::TYPE_SV11): ?>
    <?php include_partial('sv/saisieSV11', ['form' => $form, 'sv' => $sv, 'cvi' => $cvi]) ?>
  <?php else: ?>
    <?php include_partial('sv/saisieSV12', ['form' => $form, 'sv' => $sv, 'cvi' => $cvi]) ?>
  <?php endif ?>

  <div class="row">
    <?php if (isset($cvi_precedent)): ?>
      <div class="col-xs-4 text-left"><a href="<?php echo url_for('sv_saisie', ['id' => $sv->_id, 'cvi' => $cvi_precedent]) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Apporteur précédent</a></div>
    <?php else: ?>
      <div class="col-xs-4 text-left"><a href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Précédent</a></div>
    <?php endif ?>
    <div class="col-xs-4 text-center"><a href="<?php echo url_for('sv_apporteurs', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Retour à la liste des apporteurs</a></div>
    <div class="col-xs-4 text-right"><button type="submit" class="btn btn-success">Valider et continuer <span class="glyphicon glyphicon-chevron-right"></span></button></div>
  </div>
</form>

<?php if(isset($showModalExtraction) && $showModalExtraction): ?>
  <?php include_component('sv', 'modalExtraction', array('sv' => $sv, 'url' => url_for('sv_saisie', ['sf_subject' => $sv, 'cvi' => $cvi]))); ?>
<?php endif; ?>
