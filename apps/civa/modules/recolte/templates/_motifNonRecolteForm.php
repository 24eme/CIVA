<?php use_helper('jQuery') ?>

<?php echo jq_form_remote_tag(array('url' => url_for(array_merge($onglets->getUrl('recolte_motif_non_recolte', null, null, null, null)->getRawValue(), array('detail_key' => $detail_key))),
                                    'method' => 'post',
                                    'dataType' => 'json',
                                    'before' => "$('#form_motif_non_recolte input[type=image]').hide();
                                                 $('#form_motif_non_recolte .valider-loading').show();
                                                 $('#form_motif_non_recolte .valider-loading').css('display','inline-block');",
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
    <span class="valider-loading"></span>
</form>