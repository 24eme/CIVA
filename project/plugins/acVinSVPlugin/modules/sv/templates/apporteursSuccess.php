<?php include_partial('sv/step', array('object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_APPORTEURS)); ?>

<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th class="col-xs-4">Nom de l'apporteur</th>
      <th class="col-xs-1">CVI</th>
      <th class="col-xs-1">Commune</th>
      <th></th>
    </tr>
  </thead>
<?php foreach($sv->apporteurs as $apporteur): ?>
<tr>
  <td><?php echo $apporteur->nom ?></td>
  <td><?php echo $apporteur->cvi ?></td>
  <td><?php echo $apporteur->commune ?></td>
  <td class="text-right"><a href="<?php echo url_for('sv_saisie', $sv); ?>" class="btn btn-xs btn-default">Saisir</a></td>
</tr>
<?php endforeach; ?>
</table>