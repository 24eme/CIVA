<ul>
<?php foreach ($form_acheteurs as $form_acheteur): ?>
    <li>
        <?php echo $form_acheteur['quantite_vendue']->renderError() ?>
        <?php echo $form_acheteur['quantite_vendue']->render(array('class' => 'num')) ?>
    </li>
<?php endforeach; ?>
</ul>