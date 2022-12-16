<?php if (!isset($afficheLiens)) $afficheLiens = true; ?>
<?php if($validation->hasErreurs()): ?>
<fieldset class="message message_erreur">
    <legend class="message_title">Points bloquants <a href="#" class="msg_aide_ds" rel="help_popup_validation_log_erreur" title="Message aide"></a></legend>
    <?php include_partial('global/validationType', array('points' => $validation->getPoints('erreur'), 'css_class' => 'error', 'afficheLiens' => $afficheLiens)) ?>
</fieldset>
<?php endif; ?>

<?php if($validation->hasVigilances()): ?>
<fieldset class="message">
    <legend class="message_title">Points de vigilance <a href="#" class="msg_aide_ds" rel="help_popup_validation_log_vigilance_ds" title="Message aide"></a></legend>
     <?php include_partial('global/validationType', array('points' => $validation->getPoints('vigilance'), 'css_class' => 'warning', 'afficheLiens' => $afficheLiens)) ?>
    <?php if ($validation->printNoticeVigilance()): ?>
    <strong>A défaut de saisie ces produits seront supprimés automatiquement à la validation.</strong>
    <?php endif; ?>
</fieldset>
<?php endif; ?>
