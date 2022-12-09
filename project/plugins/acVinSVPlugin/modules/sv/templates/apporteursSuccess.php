<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_APPORTEURS)); ?>

<?php if(count($sv->extraction)): ?>
<a href="<?php echo url_for('sv_apporteurs', array('sf_subject' => $sv, 'parametrage_extraction' => 1)) ?>" class="pull-right btn btn-link"><span class="glyphicon glyphicon-cog"></span> Paramètrer les taux d'extraction globaux</a>
<?php endif; ?>

<h3>Liste de vos apporteurs</h3>

<p style="margin-bottom: 15px;">Texte d'intro</p>

<?php if(!count($sv->extraction)): ?>
<div class="alert alert-warning pointer">
  <a href="<?php echo url_for('sv_apporteurs', array('sf_subject' => $sv, 'parametrage_extraction' => 1)) ?>" class="pull-right"><span class="glyphicon glyphicon-cog"></span> Paramétrer les taux d'extraction globaux</a>
  <span class="glyphicon glyphicon-info-sign"></span> Afin de facilité et accélerer votre saisie vour pouvez configurer vos taux d'extraction globaux par cépage.
</div>
<?php endif; ?>

<table style="margin-top: 15px;" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-1">CVI</th>
      <th class="col-xs-3">Nom de l'apporteur</th>
      <th class="col-xs-2 text-center">Commune</th>
      <th class="col-xs-2 text-center">Superficie<br/>par apporteur</th>
      <th class="col-xs-2 text-center">Revendiqué<br/>par apporteur</th>
      <th class="col-xs-2 text-center">Statut</th>
      <th></th>
    </tr>
  </thead>
<?php foreach($sv->apporteurs as $apporteur): ?>
<tr class="vertical-center">
  <?php $recap = $apporteur->getRecapProduits(); ?>
  <td><?php echo $apporteur->cvi ?></td>
  <td><?php echo $apporteur->nom ?></td>
  <td><?php echo $apporteur->commune ?></td>
  <td class="text-right"><?php echoFloat($recap['superficie']) ?> <small class="text-muted">ares</small></td>
  <td class="text-right"><?php echoFloat($recap['revendique']) ?> <small class="text-muted">hl</small></td>
  <td class="text-center"><?php if($apporteur->getNbSaisies() >= count($apporteur->produits)): ?><span class="label label-success">Saisie complète</span><?php elseif($apporteur->getNbSaisies() > 0): ?><span class="label label-warning">Saisie en cours</span><?php endif; ?></td>
  <td class="text-right"><a href="<?php echo url_for('sv_saisie', array('sf_subject' => $sv, 'cvi' => $apporteur->getKey())); ?>" class="btn btn-xs btn-default">Saisir <span class="glyphicon glyphicon-chevron-right"></span></a></td>
</tr>
<?php endforeach; ?>
</table>

<div class="row" style="margin-top: 30px;">
  <div class="col-xs-6 text-left"><a href="<?php echo url_for('mon_espace_civa_production', $sv->getEtablissementObject()) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
  <div class="col-xs-6 text-right"><a href="<?php echo url_for('sv_autres', $sv) ?>" class="btn btn-default">Étape suivante <span class="glyphicon glyphicon-chevron-right"></span></a></div>
</div>

<?php if(isset($showModalExtraction) && $showModalExtraction): ?>
  <?php include_component('sv', 'modalExtraction', array('sv' => $sv)); ?>
<?php endif; ?>
