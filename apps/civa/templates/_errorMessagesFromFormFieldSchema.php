<?php foreach($form_field_schema as $item): ?>
    <?php if ($item instanceof sfFormFieldSchema): ?>
        <?php include_partial('global/errorMessagesFromFormFieldSchema', array('form_field_schema' => $item)) ?>
    <?php elseif($item instanceof sfFormField && $item->hasError()): ?>
        <li><?php echo $item->renderLabel() ?> : <?php echo $item->getError()->getMessage() ?></li>
    <?php endif; ?>
<?php endforeach; ?>

