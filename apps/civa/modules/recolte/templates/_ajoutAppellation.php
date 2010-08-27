<?php use_helper('jQuery') ?>

<?php echo jq_form_remote_tag(array('url' => url_for($onglets->getUrl('recolte_add_appellation')->getRawValue()),
                                    'method' => 'post',
                                    'dataType' => 'json',
                                    'update' => array('failure' => "$('#form_ajout_appellation').replaceWith('Une erreur est survenue !');"),
                                    'success' => "if (data.action == 'render') {
                                                    $('#form_ajout_appellation').replaceWith(data.data);
                                                  } else if (data.action == 'redirect') {
                                                    document.location.href = data.data;
                                                  }")
                              , array('id' => 'form_ajout_appellation')); ?>
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <?php echo $form['appellation']->renderError(); ?>
    <?php echo $form['appellation']->renderLabel("Sélectionnez le nom de l'appellation :"); ?>
    <?php echo $form['appellation']->render(); ?>

    <input type="image" name="" src="/images/boutons/btn_valider.png" alt="Valider" />
</form>
