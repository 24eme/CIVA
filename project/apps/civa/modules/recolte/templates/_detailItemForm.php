<div class="col_recolte col_active">
    <form id=" " action="<?php echo ($is_new) ? url_for($onglets->getUrl('recolte_add')->getRawValue()) : url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>" method="post" onsubmit="return valider_can_submit();">
        <?php echo $form->renderHiddenFields(); ?>
        <h2><?php echo $onglets->getCurrentCepage()->getConfig()->libelle ?></h2>

        <div class="col_cont">

            <?php if ($onglets->getCurrentAppellation()->getConfig()->hasLieuEditable()): ?>
                <p class="lieu <?php echo ($form['lieu']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                    <?php echo $form['lieu']->render(array('class' => 'premier_focus')) ?>
                </p>
            <?php endif; ?>

            <p class="denomination <?php echo ($form['denomination']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                <?php if ($onglets->getCurrentCepage()->getConfig()->hasDenomination()) : ?>
                    <?php echo $form['denomination']->render() ?>
                <?php endif; ?>
            </p>

            <p class="mention <?php echo ($form['vtsgn']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                <?php if ($onglets->getCurrentCepage()->getConfig()->hasVtsgn()) : ?>
                    <?php echo $form['vtsgn']->render() ?>
                <?php endif; ?>
            </p>

            <p class="superficie <?php echo ($form['superficie']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                <?php if ($onglets->getCurrentCepage()->getConfig()->hasSuperficie()) : ?>
                    <?php echo $form['superficie']->render(array('class' => 'superficie num premier_focus')) ?>
                <?php endif; ?>
            </p>

            <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoNegociant()): ?>
                <div class="vente_raisins">
                    <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_NEGOCES])); ?>
                    <?php if (!$onglets->getCurrentCepage()->getConfig()->hasMinQuantite()) : ?>
                    <a href="#" class="ajout ajout_acheteur" tabindex="-1">Acheteur</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoCooperative()): ?>
                <div class="caves">
                    <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_COOPERATIVES])); ?>
                    <?php if (!$onglets->getCurrentCepage()->getConfig()->hasMinQuantite()) : ?>
                    <a href="#" class="ajout ajout_cave" tabindex="-1">Cave</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($form[RecolteForm::FORM_NAME_MOUTS]) && !$onglets->getCurrentCepage()->getConfig()->hasNoMout()): ?>
                <div class="mouts">
                    <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_MOUTS])); ?>
                    <?php if (!$onglets->getCurrentCepage()->getConfig()->hasMinQuantite()) : ?>
                    <a href="#" class="ajout ajout_mout" tabindex="-1">Acheteur de mouts</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <p class="vol_place <?php echo ($form['cave_particuliere']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                <?php echo $form['cave_particuliere']->render(array('class' => 'num cave volume')) ?>
            </p>
            <p class="vol_total_recolte"><input type="text" id="detail_vol_total_recolte" class="num total readonly" tabindex="-1" readonly="readonly" value="<?php echoFloat($detail->volume) ?>" /></p>
            <?php if ($detail->getConfig()->existRendement()): ?>
                <ul class="vol_revendique_dplc">
                    <li>
                        <input id="detail_volume_revendique" type="hidden" class="revendique num readonly" readonly="readonly" value="<?php echo $detail->volume_revendique ?>" />
                    </li>
                    <li>
                        <?php if (isset($form['lies'])) : ?>
                            <?php echo $form['lies']->render(array('class' => 'num lies')) ?>
                        <?php else: ?>
                            <input id="detail_lies" type="hidden" class="lies num readonly" readonly="readonly" value="<?php echo $detail->lies ?>" />
                        <?php endif; ?>
                        
                        <input id="detail_usages_industriels" type="hidden" class="usages_industriels num readonly" readonly="readonly" value="<?php echo $detail->usages_industriels ?>" />
                    </li>
                </ul>
                <ul>
                    <li>
                    </li>
                    <li>
                        <input type="hidden" id="detail_max_volume" value="<?php echo $detail->getVolumeMax(); ?>"/>
                        <input type="hidden" id="detail_rendement" value="<?php echo $detail->getConfig()->getRendementNoeud(); ?>"/>
                        <input id="detail_dplc" type="hidden" class="dplc num readonly" readonly="readonly" value="<?php echo $detail->volume_dplc ?>" />
                    </li>
                </ul>
            <?php endif; ?>
        </div>

        <div class="col_btn">
            <a href="<?php echo url_for($onglets->getUrl('recolte')->getRawValue()); ?>" tabindex="-1" class="annuler_tmp"><img src="/images/boutons/btn_annuler_col_cepage.png" alt="Annuler" /></a>
            <script><!--
<?php if ($onglets->getCurrentCepage()->getConfig()->excludeTotal()) : ?>
        autoTotal = false;
<?php else : ?>
        autoTotal = true;
<?php endif; ?>
    function valider_can_submit() 
    {
        
<?php if ($onglets->getCurrentAppellation()->getConfig()->hasLieuEditable()) : ?>
            if (!document.getElementById('detail_lieu').value) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_dr_popup_no_lieu')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }
<?php endif; ?>

<?php if ($onglets->getCurrentCepage()->getConfig()->hasSuperficie() && $onglets->getCurrentCepage()->getConfig()->isSuperficieRequired()) : ?>
            if (!document.getElementById('detail_superficie').value || !(document.getElementById('detail_superficie').value > 0)) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_dr_popup_no_superficie')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }
<?php endif; ?>

    <?php if (isset($form['lies'])) : ?>
    if (parseFloat($('#detail_lies').val()) > parseFloat($('#detail_vol_total_recolte').val())) {
        $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_log_usages_industriels_superieur_volume')); ?></p>');
        openPopup($('#popup_msg_erreur'), 0);
        return false;
    }
    <?php endif; ?>

<?php if ($onglets->getCurrentCepage()->getConfig()->hasMinQuantite()) : ?>
            var total_non_negociant = <?php echo $onglets->getCurrentLieu()->getTotalVolumeForMinQuantite() ?>;
            var min = truncTotal(total_non_negociant * <?php echo $onglets->getCurrentCepage()->getConfig()->min_quantite ?>);
            var max = truncTotal(total_non_negociant * <?php echo $onglets->getCurrentCepage()->getConfig()->max_quantite ?>);
                
            var rebeche_ratio_respected = true;
            $("#col_recolte_totale .caves input").each(function()
            {
                if($(this).attr('type')!='hidden' && $(this).val()>0){
                    var css_classes =$(this).attr('class'). split(" ");
                    var class_cvi = css_classes[1];
                    if(parseFloat($(".col_active .caves input[class*='"+class_cvi+"']").val())==0){
                        rebeche_ratio_respected = false;
                    }
                }
            });
            if (parseFloat($('#detail_cave_particuliere').val()) == 0 && parseFloat($('#appellation_total_cave').val()) > 0) {
                rebeche_ratio_respected = false;
            }

            if (!rebeche_ratio_respected) {
                $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id' => 'err_dr_popup_dest_rebeches')); ?></p>');
                openPopup($('#popup_msg_erreur'), 0);
                return false;
            }

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

<?php endif; ?>

<?php if ($onglets->getCurrentAppellation()->getConfig()->hasLieuEditable() || $onglets->getCurrentCepage()->getConfig()->hasDenomination() || $onglets->getCurrentCepage()->getConfig()->hasVtsgn()): ?>
            var couples_unique_key = <?php echo json_encode($onglets->getCurrentCepage()->getArrayUniqueKey(array($form->getObject()->getKey()))->getRawValue()); ?>;
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