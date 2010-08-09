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
                <h3>Ventes de Raisins</h3>
                <ul>
                <?php foreach ($form[RecolteForm::FORM_NAME_NEGOCES] as $form_acheteur): ?>
                    <li>
                        <?php echo $form_acheteur['quantite_vendue']->renderError() ?>
                        <?php echo $form_acheteur['quantite_vendue']->render() ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>

            <div class="caves">
                <h3>Caves Coopératives</h3>
                <ul>
                    <?php foreach ($form[RecolteForm::FORM_NAME_COOPERATIVES] as $form_acheteur): ?>
                    <li>
                        <?php echo $form_acheteur['quantite_vendue']->renderError() ?>
                        <?php echo $form_acheteur['quantite_vendue']->render() ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>

            <p class="vol_place">
            <?php echo $form['cave_particuliere']->renderError() ?>
            <?php echo $form['cave_particuliere']->render() ?>
                </p>

                <p class="vol_total_recolte">&nbsp;</p>

                <ul class="vol_revendique_dplc">
                    <li>&nbsp;</li>
                    <li>&nbsp;</li>
                </ul>
                <input type="submit" value="Valider" />
                <a href="<?php echo url_for($onglets->getUrl('recolte')->getRawValue()); ?>">Annuler</a>
    </form>
</div>
