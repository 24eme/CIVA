<?php foreach ($verify as $code_erreur => $lines): ?>
<div class="columns">
  <div class="col-xs-4">
    <?php if ($code_erreur === SVClient::CSV_ERROR_ACHETEUR): echo "Mauvais acheteur"; endif ?>
    <?php if ($code_erreur === SVClient::CSV_ERROR_APPORTEUR): echo "Apporteur non reconnu"; endif ?>
    <?php if ($code_erreur === SVClient::CSV_ERROR_PRODUIT): echo "Produit non reconnu"; endif ?>
  </div>
  <div class="col-xs-8">
    <?php foreach ($lines as $line): ?>
      <a href='"#line".<?php echo $line[0] ?>'>#<?php echo $line[0] ?></a>
    <?php endforeach ?>
  </div>
</div>
<?php endforeach ?>

<table>

</table>
