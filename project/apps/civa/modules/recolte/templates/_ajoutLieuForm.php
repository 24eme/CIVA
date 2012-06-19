<?php use_helper('jQuery') ?>
<?php echo jq_form_remote_tag(array('url' => url_for($url->getRawValue()),
                                    'method' => 'post',
                                    'dataType' => 'json',
                                    'update' => array('failure' => "$('#form_ajout_lieu').replaceWith('Une erreur est survenue !');"),
                                    'before' => "$('#form_ajout_lieu input[type=image]').hide();
                                                 $('#form_ajout_lieu .valider-loading').show();
                                                 $('#form_ajout_lieu .valider-loading').css('display','inline-block');",
                                    'success' => "if (data.action == 'render') {
                                                    $('#form_ajout_lieu').replaceWith(data.data);
                                                  } else if (data.action == 'redirect') {
                                                    document.location.href = data.data;
                                                  }")
                              , array('id' => 'form_ajout_lieu')); ?>
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <?php echo $form['appellation']->renderError(); ?>
    <?php echo $form['appellation']->render(); ?>

    <?php echo $form['lieu']->renderError(); ?>
    <?php echo $form['lieu']->renderLabel("Sélectionnez le nom du lieu-dit :"); ?>
    <?php echo $form['lieu']->render(); ?>

    <input type="image" name="" src="/images/boutons/btn_valider.png" alt="Valider" />
    <span class="valider-loading"></span>
</form>
