
<ul>
<li style="display:none"></li>
<?php foreach ($form_acheteurs as $form_acheteur): ?>
    <?php include_partial('formAcheteursItem', array('form' => $form_acheteur, 'type' => $form_acheteurs->getName())) ?>
<?php endforeach; ?>
</ul>