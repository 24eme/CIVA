<?php if ($validation->hasErreurs()): ?>
  <h3>Points bloquants</h3>
  <?php foreach ($validation->getErreurs() as $erreur): ?>
  <div class="alert alert-danger">
    <?php echo $erreur ?>
  </div>
  <?php endforeach ?>
<?php endif ?>

<?php if ($validation->hasVigilances()): ?>
  <h3>Points de vigilance</h3>
  <?php foreach ($validation->getVigilances() as $vigilance): ?>
  <div class="alert alert-warning">
    <?php echo $vigilance->getMessage() ?>
  </div>
  <?php endforeach ?>
<?php endif ?>

