<?php if ($form instanceof sfForm && $form->hasErrors()): ?>
<?php echo $form->renderGlobalErrors(); ?>
<?php foreach($form as $widget): ?>
    <?php if ($widget->hasError()): ?>
        <?php echo $widget->renderLabel(); ?>
        <?php echo $widget->renderError(); ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>