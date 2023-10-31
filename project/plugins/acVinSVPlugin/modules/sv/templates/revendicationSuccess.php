<?php use_helper('Float'); ?>
<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_REVENDICATION)); ?>

<a href="<?php echo url_for('sv_recalcule_volumes_revendiques', $sv) ?>" class="pull-right btn btn-link"><span class="glyphicon glyphicon-refresh"></span> Recalculer les volumes revendiqués</a>

<h3>Liste de vos apporteurs</h3>

<p style="margin-bottom: 15px;">Saisissez ici les données de production de tous vos apporteurs.</p>

<table style="margin-top: 15px;" class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-1">CVI</th>
      <th class="col-xs-3">Nom de l'apporteur</th>
      <th class="col-xs-2 text-center">Commune</th>
      <th class="col-xs-2 text-center">Superficie<br/>par apporteur</th>
      <th class="col-xs-2 text-center">Revendiqué<br/>par apporteur</th>
      <th class="col-xs-2 text-center"></th>
      <th></th>
    </tr>
  </thead>
<?php foreach($sv->apporteurs as $apporteur): ?>
<tr class="vertical-center">
  <?php $recap = $apporteur->getRecapProduits(); ?>
  <td><?php echo $apporteur->cvi ?></td>
  <td><?php echo $apporteur->nom ?></td>
  <td><?php echo $apporteur->commune ?></td>
  <td class="text-right">
      <?php if ($recap['superficie'] && $recap['mouts_superficie']): ?>
        (R+M) <?php echoFloat($recap['superficie'] + $recap['mouts_superficie']) ?>
      <?php elseif ($recap['mouts_superficie']): ?>
        (M) <?php echoFloat($recap['mouts_superficie']) ?>
      <?php else : ?>
        <?php echoFloat($recap['superficie']) ?>
      <?php endif ?>

      <small class="text-muted">ares</small>
  </td>
  <td class="text-right">
      <?php if($recap['revendique'] || $recap['mouts_revendique']): ?>
        <?php if($recap['revendique'] && $recap['mouts_revendique']): ?>
        (R+M) <?php echoFloat($recap['revendique'] + $recap['mouts_revendique']) ?>
        <?php elseif($recap['mouts_revendique']): ?>
        (M) <?php echoFloat($recap['mouts_revendique']) ?>
        <?php else: ?>
        <?php echoFloat($recap['revendique']) ?>
        <?php endif; ?>
        <small class="text-muted">hl</small>
      <?php endif; ?>
  </td>
  <td class="text-center"><?php if($apporteur->isComplete()): ?><span class="label label-success">Saisie complète</span><?php elseif($apporteur->getNbSaisies() > 0): ?><span class="label label-warning">Saisie en cours</span><?php endif; ?></td>
  <td class="text-right"><a href="<?php echo url_for('sv_saisie_revendication', array('sf_subject' => $sv, 'cvi' => $apporteur->getKey())); ?>" class="btn btn-xs btn-default">Saisir <span class="glyphicon glyphicon-chevron-right"></span></a></td>
</tr>
<?php endforeach; ?>
</table>

<div class="row" style="margin-top: 30px;">
  <div class="col-xs-4 text-left"><a href="<?php echo url_for('sv_extraction', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Retourner à mon espace</a></div>
  <div class="col-xs-4 text-center"></div>
  <div class="col-xs-4 text-right"><a href="<?php echo url_for('sv_autres', $sv) ?>" class="btn btn-default">Étape suivante <span class="glyphicon glyphicon-chevron-right"></span></a></div>
</div>
