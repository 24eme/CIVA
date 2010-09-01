<?php use_helper('jQuery') ?>

<?php echo jq_form_remote_tag(array('url' => url_for(array_merge($onglets->getUrl('recolte_motif_non_recolte')->getRawValue(), array('detail_key' => $detail_key))),
                                    'method' => 'post',
                                    'dataType' => 'json',
                                    'update' => array('failure' => "$('#form_motif_non_recolte').replaceWith('Une erreur est survenue !');"),
                                    'success' => "if (data.action == 'render') {
                                                    $('#form_motif_non_recolte').replaceWith(data.data);
                                                  } else if (data.action == 'redirect') {
                                                    document.location.href = data.data;
                                                  }")
                              , array('id' => 'form_motif_non_recolte')); ?>
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <?php echo $form['motif_non_recolte']->renderError(); ?>
    <?php echo $form['motif_non_recolte']->renderLabel("Sélectionnez le motif de non saisie du volume :"); ?>
    <?php echo $form['motif_non_recolte']->render(); ?>

    <input type="image" name="" src="/images/boutons/btn_valider.png" alt="Valider" />
</form>