<?php /* if ($form instanceof sfForm && $form->hasErrors()): ?>
<?php echo $form->renderGlobalErrors(); ?>
<?php foreach($form as $widget): ?>
    <?php if ($widget->hasError()): ?>
        <?php echo $widget->renderLabel(); ?>
        <?php echo $widget->renderError(); ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php endif; */?>

<?php if ($form instanceof sfForm && ($form->hasErrors() || $form->hasGlobalErrors())): ?>
<ul class="error_list">
    <?php foreach($form->getGlobalErrors() as $item): ?>
        <li><?php echo $item->getMessage(); ?></li>
    <?php endforeach; ?>
    <?php include_partial('global/errorMessagesFromFormFieldSchema', array('form_field_schema' => $form->getFormFieldSchema())) ?>
</ul>
<?php endif; ?>