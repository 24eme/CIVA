<?php use_helper('jQuery') ?>
<?php echo jq_form_remote_tag(array('url' => url_for($url->getRawValue()),
                                    'method' => 'post',
                                    'dataType' => 'html',
                                    'success' => "$('#form_ajout_lieu').replaceWith(data);")
                              , array('id' => 'form_ajout_lieu')); ?>
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <?php echo $form['appellation']->renderError(); ?>
    <?php echo $form['appellation']->render(); ?>

    <?php echo $form['lieu']->renderError(); ?>
    <?php echo $form['lieu']->renderLabel("Sélectionnez le nom du lieu-dit :"); ?>
    <?php echo $form['lieu']->render(); ?>

    <input type="image" name="" src="/images/boutons/btn_valider.png" alt="Valider" />
</form>
