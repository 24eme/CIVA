<form action="<?php echo url_for('@login') ?>" method="post">
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <?php echo $form['cvi']->renderError() ?>
    <?php echo $form['cvi']->renderLabel() ?>
    <?php echo $form['cvi']->render() ?>
    
    <input type="submit" value="Valider" />
</form>