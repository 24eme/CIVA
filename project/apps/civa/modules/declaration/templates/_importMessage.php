<p><?php echo acCouchdbManager::getClient('Messages')->getMessage('msg_declaration_ecran_warning_pre_import') ?></p>

<ul>
<?php foreach ($acheteurs as $acheteur): ?>
    <li><?php echo $acheteur->nom ?></li>
<?php endforeach ?>
</ul>

<?php if(isset($post_message)): ?>
<br />
<p><?php echo acCouchdbManager::getClient('Messages')->getMessage('msg_declaration_ecran_warning_post_import') ?></p>
<?php endif; ?>