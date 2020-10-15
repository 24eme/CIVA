<div class="col_recolte col_active">
    <form action="<?php echo ($is_new) ? url_for('dr_recolte_produit_ajout', array('sf_subject' => $produit->getDocument(), 'hash' => $produit->getHash())) : url_for('dr_recolte_produit_edition', array('id' => $produit->getDocument()->_id, 'hash' => $produit->getHash(), 'detail_key' => $detail->getKey())) ?>" method="post" onsubmit="return valider_can_submit();">
        <?php echo $form->renderHiddenFields(); ?>
        <h2><?php echo $produit->libelle ?></h2>

        <div class="col_cont">

            <?php if ($produit->getAppellation()->getConfig()->hasLieuEditable()): ?>
                <p class="lieu <?php echo ($form['lieu']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                    <?php echo $form['lieu']->render(array('class' => 'premier_focus')) ?>
                </p>
            <?php endif; ?>

            <p class="denomination <?php echo ($form['denomination']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                <?php if ($produit->getConfig()->hasDenomination()) : ?>
                    <?php echo $form['denomination']->render() ?>
                <?php endif; ?>
            </p>

            <p class="mention <?php echo ($form['vtsgn']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                <?php if ($produit->getConfig()->hasVtsgn()) : ?>
                    <?php echo $form['vtsgn']->render() ?>
                <?php endif; ?>
            </p>

            <p class="superficie <?php echo ($form['superficie']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                <?php if ($produit->getConfig()->hasSuperficie()) : ?>
                    <?php echo $form['superficie']->render(array('class' => 'superficie num premier_focus')) ?>
                <?php endif; ?>
            </p>

            <?php if (!$produit->getConfig()->hasNoNegociant()): ?>
                <div class="vente_raisins">
                    <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_NEGOCES])); ?>
                    <?php if (!$produit->getConfig()->hasMinQuantite()) : ?>
                    <a href="#" class="ajout ajout_acheteur" tabindex="-1">Acheteur</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!$produit->getConfig()->hasNoCooperative()): ?>
                <div class="caves">
                    <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_COOPERATIVES])); ?>
                    <?php if (!$produit->getConfig()->hasMinQuantite()) : ?>
                    <a href="#" class="ajout ajout_cave" tabindex="-1">Cave</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($form[RecolteForm::FORM_NAME_MOUTS]) && !$produit->getConfig()->hasNoMout()): ?>
                <div class="mouts">
                    <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_MOUTS])); ?>
                    <?php if (!$produit->getConfig()->hasMinQuantite()) : ?>
                    <a href="#" class="ajout ajout_mout" tabindex="-1">Acheteur de mouts</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <p class="vol_place <?php echo ($form['cave_particuliere']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                <?php echo $form['cave_particuliere']->render(array('class' => 'num cave volume')) ?>
            </p>
            <p class="vol_total_recolte"><input type="text" id="detail_vol_total_recolte" class="num total readonly" tabindex="-1" readonly="readonly" value="<?php echoFloat($detail->volume) ?>" /></p>
                <ul class="vol_revendique_dplc">
                    <?php if ($detail->getConfig()->existRendement()): ?>
                    <li>
                        <input id="detail_volume_revendique" tabindex="-1" type="<?php echo (isset($form['lies'])) ? "text" : "hidden" ?>" class="revendique num readonly" readonly="readonly" value="<?php echoFloat($detail->volume_revendique) ?>" />
                    </li>
                    <li>
                        <?php if (isset($form['lies'])) : ?>
                            <?php echo $form['lies']->render() ?>
                        <?php else: ?>
                            <input id="detail_lies" type="hidden" class="lies num readonly" readonly="readonly" value="<?php echo $detail->lies ?>" />
                        <?php endif; ?>

                        <input id="detail_usages_industriels" type="hidden" class="usages_industriels num readonly" readonly="readonly" value="<?php echo $detail->usages_industriels ?>" />
                    </li>
                    <?php endif; ?>
                    <?php if($produit->canHaveVci()): ?>
                    <li>
                        <?php if (isset($form['vci'])) : ?>
                            <?php echo $form['vci']->render(array('class' => 'vci num')) ?>
                        <?php else: ?>
                            <input id="detail_vci" type="hidden" class="vci num readonly" readonly="readonly" value="<?php echo $detail->lies ?>" />
                        <?php endif; ?>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul>
                    <li></li>
                    <li></li>
                </ul>
        </div>

        <div class="col_btn">
            <a href="" tabindex="-1" class="annuler_tmp"><img src="/images/boutons/btn_annuler_col_cepage.png" alt="Annuler" /></a>
            <script><!--
<?php if ($produit->getConfig()->excludeTotal()) : ?>
        autoTotal = false;
<?php else : ?>
        autoTotal = true;
<?php endif; ?>
    function valider_can_submit()
    {

<?php if ($produit->getAppellation()->getConfig()->hasLieuEditable()) : ?>
            if (!document.getElementById('detail_lieu').value) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_dr_popup_no_lieu')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }
<?php endif; ?>

<?php if ($produit->getConfig()->hasSuperficie() && $produit->getConfig()->isSuperficieRequired()) : ?>
            if (!document.getElementById('detail_superficie').value || !(document.getElementById('detail_superficie').value > 0)) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_dr_popup_no_superficie')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }
<?php endif; ?>

    <?php if (isset($form['lies'])) : ?>
    // var inputs_mouts = $(".col_active .mouts input[class*='acheteur_mouts_']");
    // if(!inputs_mouts.length) {
        if (parseFloat($('#detail_lies').val()) > 0 && (!$('#detail_cave_particuliere').val() || parseFloat($('#detail_cave_particuliere').val()) == 0)) {
            $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_log_usages_industriels_pas_volume_sur_place')); ?></p>');
            openPopup($('#popup_msg_erreur'), 0);
            return false;
        }
        if(parseFloat($('#detail_lies').val()) > parseFloat($('#detail_cave_particuliere').val())) {
            $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_log_usages_industriels_superieur_volume_sur_place')); ?></p>');
            openPopup($('#popup_msg_erreur'), 0);
            return false;
        }
    // }
    <?php endif; ?>

<?php if ($produit->getConfig()->hasMinQuantite()) : ?>
            var total_non_negociant = <?php echo $produit->getLieu()->getTotalVolumeForMinQuantite() ?>;
            var min = truncTotal(total_non_negociant * <?php echo $produit->getConfig()->get('attributs/min_quantite') ?>);
            var max = truncTotal(total_non_negociant * <?php echo $produit->getConfig()->get('attributs/max_quantite') ?>);

            var min_quantite = <?php echo $produit->getConfig()->get('attributs/min_quantite') ?>;
            var max_quantite = <?php echo $produit->getConfig()->get('attributs/max_quantite') ?>;

            var volume_cooperatives = <?php echo json_encode($produit->getLieu()->getVolumeAcheteursForMinQuantite()->getRawValue()) ?>;
            var volume_cave_particuliere = <?php echo $produit->getLieu()->getTotalCaveParticuliereForMinQuantite() ?>;

            if (parseFloat($('#detail_vol_total_recolte').val()) < min) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_dr_popup_min_quantite')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }

            if (parseFloat($('#detail_vol_total_recolte').val()) > max) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_max_quantite')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }

            for(cvi in volume_cooperatives) {
                volume = volume_cooperatives[cvi];
                volume_saisie = 0;
                var input_cooperative = $(".col_active .caves input[class*='acheteur_cooperatives_"+cvi+"']");
                if(input_cooperative.length > 0 && input_cooperative.val()) {
                    volume_saisie = input_cooperative.val();
                }
                if(parseFloat(volume_saisie) < truncTotal(parseFloat(volume * min_quantite))) {
                    $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_dest_rebeches')); ?></p>');
                    openPopup($('#popup_msg_erreur'), 0);
                    return false;
                }
                if(parseFloat(volume_saisie) > truncTotal(parseFloat(volume * max_quantite))) {
                    $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_dest_rebeches')); ?></p>');
                    openPopup($('#popup_msg_erreur'), 0);
                    return false;
                }
            }

            var volume_cave_saisie = parseFloat($('.col_active #detail_cave_particuliere').val());

            if(volume_cave_saisie < truncTotal(parseFloat(volume_cave_particuliere * min_quantite))) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_dest_rebeches')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }

            if(volume_cave_saisie > truncTotal(parseFloat(volume_cave_particuliere * max_quantite))) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_dest_rebeches')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }

<?php endif; ?>

<?php if ($produit->getAppellation()->getConfig()->hasLieuEditable() || $produit->getConfig()->hasDenomination() || $produit->getConfig()->hasVtsgn()): ?>
            var couples_unique_key = <?php echo json_encode($produit->getArrayUniqueKey(array($form->getObject()->getKey()))->getRawValue()); ?>;
            var lieu_val = '';
            if($('.col_recolte.col_active .col_cont p.lieu input').length > 0) {
                lieu_val = $('.col_recolte.col_active .col_cont p.lieu input').val();
            }
            var denomination_val = '';
            if($('.col_recolte.col_active .col_cont p.denomination input').length > 0) {
                denomination_val = $('.col_recolte.col_active .col_cont p.denomination input').val();
            }
            var mention_val = '';
            if($('.col_recolte.col_active .col_cont p.mention select').length > 0) {
                mention_val = $('.col_recolte.col_active .col_cont p.mention select').val();
            }

            var couple_item_key_current = 'lieu:'+lieu_val+',denomination:'+denomination_val+',vtsgn:'+mention_val;
            var couple_unique_key_current_is_bad = false;
            for(var couple_item_key in couples_unique_key) {
                couple_unique_key_current_is_bad = (couples_unique_key[couple_item_key] == couple_item_key_current);
                if (couple_unique_key_current_is_bad) {
                    break;
                }
            }
            if (couple_unique_key_current_is_bad) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_dr_popup_unique_lieu_denomination_vtsgn')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }
<?php endif; ?>

          return true;
      }
      --></script>
            <input type="image" src="/images/boutons/btn_valider_col_cepage.png" class="valider_tmp"/>
            <!--<a href="javascript:void(0)" class="valider_tmp"><img src="" alt="Valider" onclick="valider_can_submit(); return false;" /></a>-->
        </div>
    </form>
</div>
<div id="popup_msg_erreur" class="popup_ajout" title="Erreur !">
</div>
