<?php if ($validation->hasErreurs()): ?>
  <h3>Points bloquants</h3>
  <?php foreach ($validation->getErreurs() as $erreur): ?>
  <div class="alert alert-danger">
    <?php echo $erreur->getMessage() ?>
  </div>
  <?php endforeach ?>
<?php endif ?>

<?php if ($validation->hasErreurs()): ?>
  <?php foreach ($validation->getVigilances() as $vigilance): ?>
  <div class="alert alert-warning">
    <?php echo $vigilance->getMessage() ?>
  </div>
  <?php endforeach ?>
<?php endif ?>

