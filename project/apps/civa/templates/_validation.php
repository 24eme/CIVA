<?php if($validation->hasErreurs()): ?>
<fieldset class="message message_erreur">
    <legend class="message_title">Points bloquants <a href="#" class="msg_aide" rel="help_popup_validation_log_erreur" title="Message aide"></a></legend>
    <?php include_partial('global/validationType', array('points' => $validation->getPoints('erreur'), 'css_class' => 'error')) ?>
</fieldset>
<?php endif; ?>

<?php if($validation->hasVigilances()): ?>
<fieldset class="message">
    <legend class="message_title">Points de vigilance <a href="#" class="msg_aide" rel="help_popup_validation_log_vigilance" title="Message aide"></a></legend>
     <?php include_partial('global/validationType', array('points' => $validation->getPoints('vigilance'), 'css_class' => 'warning')) ?>
    <strong>A défaut de saisie ces produits seront supprimés automatiquement à la validation.</strong>
</fieldset>
<?php endif; ?>
