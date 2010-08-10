<div id="colonne_intitules" style="margin-left: 2px;">
    <form action="<?php echo ($is_new) ? url_for($onglets->getUrl('recolte_add')->getRawValue()) : url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>" method="post">
        <?php echo $form->renderHiddenFields(); ?>
        <?php echo $form->renderGlobalErrors(); ?>
        <ul class="denomination_mention">
            <li>
                <?php echo $form['denomination']->renderError() ?>
                <?php echo $form['denomination']->render() ?>
            </li>
            <li>
                <?php echo $form['vtsgn']->renderError() ?>
                <?php echo $form['vtsgn']->render() ?>
            </li>

        </ul>

        <p class="superficie">
            <?php echo $form['superficie']->renderError() ?>
            <?php echo $form['superficie']->render() ?>
            </p>

            <div class="vente_raisins">
                <?php include_partial('detailItemFormAcheteurs', array('title' => "Ventes de Raisins",
                                                                       'form_acheteurs' => $form[RecolteForm::FORM_NAME_NEGOCES])); ?>
            </div>

            <div class="caves">
                <?php include_partial('detailItemFormAcheteurs', array('title' => "Caves Coopératives",
                                                                       'form_acheteurs' => $form[RecolteForm::FORM_NAME_COOPERATIVES])); ?>
            </div>
            <?php if (isset($form[RecolteForm::FORM_NAME_MOUTS])): ?>
            <div class="caves">
                <?php include_partial('detailItemFormAcheteurs', array('title' => "Acheteurs de Mouts",
                                                                       'form_acheteurs' => $form[RecolteForm::FORM_NAME_MOUTS])); ?>
            </div>
            <?php endif; ?>

            <p class="vol_place">
            <?php echo $form['cave_particuliere']->renderError() ?>
            <?php echo $form['cave_particuliere']->render() ?>
                </p>

                <p class="vol_total_recolte"><?php echo $detail->volume ?>&nbsp;</p>

                <ul class="vol_revendique_dplc">
                    <li><?php echo $detail->volume_revendique ?>&nbsp;</li>
                    <li><?php echo $detail->volume_dplc ?>&nbsp;</li>
                </ul>
                <input type="submit" value="Valider" />
                <a href="<?php echo url_for($onglets->getUrl('recolte')->getRawValue()); ?>">Annuler</a>
    </form>
</div>
