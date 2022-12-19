<?php include_partial('sv/breadcrumb', array('sv' => $sv)); ?>

<a href="<?php echo url_for('mon_espace_civa_production', $sv->getEtablissementObject()) ?>" class="btn btn-default btn-sm pull-right"><span class="glyphicon glyphicon-chevron-left"></span> Retour à mon espace</a>
<h3>Vérification de votre fichier d'import</h3>

<p style="margin-top: 20px; margin-bottom: 15px;">Le fichier que vous avez importé comporte des erreurs. Vous pouvez consulter ci-dessous les différentes erreurs ligne par ligne.
</p>

<div class="alert alert-danger">
    <strong>Points bloquants</strong>
    <ul class="list-unstyled">
    <?php foreach ($verify as $code_erreur => $lines): ?>
      <li>
        <?php if ($code_erreur === SVClient::CSV_ERROR_ACHETEUR): echo "La ligne concerne un autre acheteur"; endif ?>
        <?php if ($code_erreur === SVClient::CSV_ERROR_APPORTEUR): echo "L'apporteur n'a pas été reconnu"; endif ?>
        <?php if ($code_erreur === SVClient::CSV_ERROR_PRODUIT): echo "Le produit n'a pas été reconnu"; endif ?>
        <?php if ($code_erreur === SVClient::CSV_ERROR_SUPERFICIE): echo "La superficie a été oublié"; endif ?>
        <?php if ($code_erreur === SVClient::CSV_ERROR_VOLUME): echo "La volume livré a été oublié"; endif ?>
        <?php if ($code_erreur === SVClient::CSV_ERROR_QUANTITE): echo "La quantité a été oublié"; endif ?>
        <?php if ($code_erreur === SVClient::CSV_ERROR_VOLUME_REVENDIQUE_SV11): echo "La volume revendiqué n'est pas en adéquation avec le volume livrée, détruit et le VCI"; endif ?>
        <?php if ($code_erreur === SVClient::CSV_ERROR_VOLUME_REVENDIQUE_SV12): echo "Le volume revendiqué a été oublié"; endif ?> :
          <?php foreach ($lines as $line): ?>
            <?php $lignes_en_erreur[] = $line[0]; ?>
            <a href='#line<?php echo $line[0] ?>'>#<?php echo $line[0] ?></a>
          <?php endforeach ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<p style="margin-top: 20px; margin-bottom: 20px;">Une fois que vous aurez corrigé les erreurs dans votre fichier vous pourrez : <a class="btn btn-warning btn-xs" href="<?php echo url_for('mon_espace_civa_production', $sv->getEtablissementObject()) ?>"><span class="glyphicon glyphicon-refresh"></span> Réimporter un nouveau fichier</a></p>

<table class="table table-striped table-bordered table-condensed small">
  <thead>
    <tr>
      <th>#</th>
      <th>CVI acheteur</th>
      <th>nom Acheteur</th>
      <th>CVI récoltant</th>
      <th>nom récoltant</th>
      <th>appellation</th>
      <th>lieu</th>
      <th>cépage</th>
      <th>vtsgn</th>
      <th>dénomination</th>
      <th>superficie livrée</th>
      <th>qté livrée en kg</th>
      <th>volume livré</th>
      <th>volume à détruire</th>
      <th>dont VCI</th>
      <th>volume revendiqué</th>
    </tr>
  </thead>
  <tbody>
  <?php $loop_index = 0 ?>
  <?php foreach ($csv->getCsv() as $line): ?>
  <?php $loop_index++ ?>
    <tr id="line<?php echo $loop_index ?>" class="<?php echo (in_array($loop_index, $lignes_en_erreur)) ? 'text-danger strong' : '' ?>">
      <td><?php echo $loop_index; ?></td>
      <?php foreach($line as $col): ?>
        <td>
          <?php echo trim($col); ?>
        </td>
      <?php endforeach ?>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
