<ul>
<?php foreach ($form_acheteurs as $form_acheteur): ?>
    <li class="<?php echo ($form_acheteur['quantite_vendue']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
        <?php echo $form_acheteur['quantite_vendue']->renderError() ?>
        <?php echo $form_acheteur['quantite_vendue']->render(array('class' => 'num')) ?>
    </li>
<?php endforeach; ?>
</ul>