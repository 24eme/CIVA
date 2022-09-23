<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_APPORTEURS)); ?>

<h3>Liste de vos apporteurs</h3>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-3">Nom de l'apporteur</th>
      <th class="col-xs-1">CVI</th>
      <th class="col-xs-6 text-center">Commune</th>
      <th class="col-xs-2">Statut</th>
      <th></th>
    </tr>
  </thead>
<?php foreach($sv->apporteurs as $apporteur): ?>
<tr>
  <td><?php echo $apporteur->nom ?></td>
  <td><?php echo $apporteur->cvi ?></td>
  <td><?php echo $apporteur->commune ?></td>
  <td class="text-center"><?php if($apporteur->getNbSaisies() >= count($apporteur->produits)): ?><span class="label label-success">Saisie complète</span><?php elseif($apporteur->getNbSaisies() > 0): ?><span class="label label-warning">Saisie en cours</span><?php elseif($apporteur->getNbSaisies() == 0): ?><span class="text-muted">À saisir</span><?php endif; ?></td>
  <td class="text-right"><a href="<?php echo url_for('sv_saisie', array('sf_subject' => $sv, 'cvi' => $apporteur->getKey())); ?>" class="btn btn-xs btn-default">Saisir <span class="glyphicon glyphicon-chevron-right"></span></a></td>
</tr>
<?php endforeach; ?>
</table>

<div class="row">
  <div class="col-xs-6 text-left"><a href="<?php echo url_for('sv_produits', $sv) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Étape précédente</a></div>
  <div class="col-xs-6 text-right"><a href="<?php echo url_for('sv_saisie', $sv) ?>" class="btn btn-default">Étape suivante <span class="glyphicon glyphicon-chevron-right"></span></a></div>
</div>