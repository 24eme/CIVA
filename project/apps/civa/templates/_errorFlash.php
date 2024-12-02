<?php if ($sf_user->hasFlash('error')): ?>
  <p style="margin: 0 20px 20px 20px;padding: 10px;" class="message_erreur"><?php echo $sf_user->getFlash('error') ?></p>
<?php endif; ?>
