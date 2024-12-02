<?php if ($sf_user->hasFlash('error')): ?>
  <p class="message_erreur"><?php echo $sf_user->getFlash('error') ?></p>
<?php endif; ?>
