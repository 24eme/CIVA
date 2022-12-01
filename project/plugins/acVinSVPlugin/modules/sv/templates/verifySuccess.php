<div class="columns">
  <div class="col-xs-4"><strong>Erreurs</strong></div>
  <div class="col-xs-8"><strong>Lignes</strong></div>

  <?php $lignes_en_erreur = []; ?>
  <?php foreach ($verify as $code_erreur => $lines): ?>
  <div class="col-xs-4">
    <?php if ($code_erreur === SVClient::CSV_ERROR_ACHETEUR): echo "La ligne concerne un autre acheteur."; endif ?>
    <?php if ($code_erreur === SVClient::CSV_ERROR_APPORTEUR): echo "Apporteur non reconnu"; endif ?>
    <?php if ($code_erreur === SVClient::CSV_ERROR_PRODUIT): echo "Produit non reconnu"; endif ?>
  </div>
  <div class="col-xs-8">
    <?php foreach ($lines as $line): ?>
      <?php $lignes_en_erreur[] = $line[0]; ?>
      <a href='#line<?php echo $line[0] ?>'>#<?php echo $line[0] ?></a>
    <?php endforeach ?>
  </div>
  <?php endforeach ?>
</div>

<table class="table table-striped table-bordered">
  <tbody>
  <?php $loop_index = 0 ?>
  <?php foreach (explode("\n", $csv) as $linecontent): ?>
  <?php $loop_index++ ?>
  <?php $line = str_getcsv($linecontent, ";"); ?>
    <tr id="line<?php echo $loop_index ?>" class="<?php echo (in_array($loop_index, $lignes_en_erreur)) ? 'text-danger' : '' ?>">
      <td><?php echo $loop_index; ?></td>
      <?php foreach($line as $col): ?>
        <td>
          <?php if ($loop_index === 1) { echo "<strong>"; } ?>
          <?php echo trim($col); ?>
          <?php if ($loop_index === 1) { echo "</strong>"; } ?>
        </td>
      <?php endforeach ?>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
