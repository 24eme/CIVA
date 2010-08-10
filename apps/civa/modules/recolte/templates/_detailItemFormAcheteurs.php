<h3><?php echo $title ?></h3>
<ul>
<?php foreach ($form_acheteurs as $form_acheteur): ?>
    <li>
        <?php echo $form_acheteur['quantite_vendue']->renderError() ?>
        <?php echo $form_acheteur['quantite_vendue']->render() ?>
    </li>
<?php endforeach; ?>
</ul>