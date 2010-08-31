<ul>
<?php foreach ($form_acheteurs as $form_acheteur): ?>
    <?php include_partial('formAcheteursItem', array('form' => $form_acheteur)) ?>
<?php endforeach; ?>
</ul>