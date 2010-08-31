<li class="<?php echo ($form['quantite_vendue']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
    <?php echo $form['quantite_vendue']->renderError() ?>
    <?php echo $form['quantite_vendue']->render(array('class' => 'num')) ?>
</li>