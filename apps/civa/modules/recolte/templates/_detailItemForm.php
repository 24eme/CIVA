<div class="col_recolte col_active">
    <form id="form_detail" action="<?php echo ($is_new) ? url_for($onglets->getUrl('recolte_add')->getRawValue()) : url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>" method="post">
        <?php echo $form->renderHiddenFields(); ?>
    <h2><?php echo $onglets->getCurrentCepage()->getConfig()->libelle ?></h2>

    <div class="col_cont">

        <p class="denomination <?php echo ($form['denomination']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
           <?php echo $form['denomination']->render() ?>
        </p>

        <p class="mention <?php echo ($form['vtsgn']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
            <?php echo $form['vtsgn']->render() ?>
        </p>

        <p class="superficie <?php echo ($form['superficie']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
            <?php echo $form['superficie']->render(array('class' => 'num')) ?>
        </p>

        <div class="vente_raisins">
            <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_NEGOCES])); ?>
            <a href="#" class="ajout_acheteur">Ajouter un acheteur</a>
        </div>

        <div class="caves">
            <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_COOPERATIVES])); ?>
            <a href="#" class="ajout_cave">Ajouter une cave</a>
        </div>

        <?php if (isset($form[RecolteForm::FORM_NAME_MOUTS])): ?>
        <div class="mouts">
            <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_MOUTS])); ?>
            <a href="#" class="ajout_cave">Ajouter mout</a>
        </div>
        <?php endif; ?>

        <p class="vol_place <?php echo ($form['cave_particuliere']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
            <?php echo $form['cave_particuliere']->render(array('class' => 'num')) ?>
        </p>
        <p class="vol_total_recolte"><input type="text" class="num" readonly="readonly" value="<?php echo $detail->volume_revendique ?>" /></p>
        <ul class="vol_revendique_dplc">
            <li><input type="text" class="num" readonly="readonly" value="<?php echo $detail->volume_revendique ?>" /></li>
            <li><input type="text" class="num" readonly="readonly" value="<?php echo $detail->volume_dplc ?>" /></li>
        </ul>
    </div>

    <div class="col_btn">
        <a href="<?php echo url_for($onglets->getUrl('recolte')->getRawValue()); ?>" class="annuler_tmp"><img src="/images/boutons/btn_annuler_col_cepage.png" alt="Annuler" /></a>
        <a href="#" class="valider_tmp"><img src="/images/boutons/btn_valider_col_cepage.png" alt="Valider" onclick="document.getElementById('form_detail').submit(); return false;" /></a>
    </div>
    </form>
</div>
