<div class="col_recolte col_active">
    <form id="form_detail" action="<?php echo ($is_new) ? url_for($onglets->getUrl('recolte_add')->getRawValue()) : url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>" method="post" onsubmit="return valider_can_submit();">
        <?php echo $form->renderHiddenFields(); ?>
    <h2><?php echo $onglets->getCurrentCepage()->getConfig()->libelle ?></h2>

    <div class="col_cont">

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
            <?php echo $form['superficie']->render(array('class' => 'superficie num')) ?>
<?php endif; ?>
        </p>

        <?php  if (!$onglets->getCurrentCepage()->getConfig()->hasNoNegociant()): ?>
        <div class="vente_raisins">
            <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_NEGOCES])); ?>
            <a href="#" class="ajout ajout_acheteur" tabindex="-1">Acheteur</a>
        </div>
        <?php endif; ?>

        <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoCooperative()): ?>
        <div class="caves">
            <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_COOPERATIVES])); ?>
            <a href="#" class="ajout ajout_cave" tabindex="-1">Cave</a>
        </div>
        <?php endif; ?>

        <?php if (isset($form[RecolteForm::FORM_NAME_MOUTS]) && !$onglets->getCurrentCepage()->getConfig()->hasNoMout()): ?>
        <div class="mouts">
            <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_MOUTS])); ?>
            <a href="#" class="ajout ajout_mout" tabindex="-1">Acheteur de mouts</a>
        </div>
        <?php endif; ?>

        <p class="vol_place <?php echo ($form['cave_particuliere']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
            <?php echo $form['cave_particuliere']->render(array('class' => 'num cave volume')) ?>
        </p>
        <p class="vol_total_recolte"><input type="text" id="detail_vol_total_recolte" class="num total readonly" tabindex="-1" readonly="readonly" value="<?php echo $detail->volume ?>" /></p>
        <?php if ($detail->hasRendementCepage()): ?>
        <ul class="vol_revendique_dplc">
	   <input type="hidden" id="detail_max_volume" value="<?php echo $detail->getVolumeMax(); ?>"/>
	   <input type="hidden" id="detail_rendement" value="<?php echo $detail->getRendementCepage(); ?>"/>
            <li><input id="detail_volume_revendique" type="hidden" class="revendique num readonly" readonly="readonly" value="<?php echo $detail->volume_revendique ?>" /></li>
            <li><input id="detail_volume_dplc" type="hidden" class="dplc num readonly" readonly="readonly" value="<?php echo $detail->volume_dplc ?>" /></li>
        </ul>
        <?php endif; ?>
    </div>

    <div class="col_btn">
        <a href="<?php echo url_for($onglets->getUrl('recolte')->getRawValue()); ?>" tabindex="-1" class="annuler_tmp"><img src="/images/boutons/btn_annuler_col_cepage.png" alt="Annuler" /></a>
<script><!--
<?php if ($onglets->getCurrentCepage()->excludeTotal()) : ?>
autoTotal = false;
<?php else : ?>
autoTotal = true;
<?php endif; ?>
function valider_can_submit() 
{
<?php if ($onglets->getCurrentCepage()->getConfig()->hasSuperficie() && $onglets->getCurrentCepage()->getConfig()->isSuperficieRequired()) : ?>
  if (!document.getElementById('recolte_superficie').value || !(document.getElementById('recolte_superficie').value > 0)) {
    $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_no_superficie')); ?></p>');
    openPopup($('#popup_msg_erreur'), 0);
    return false;
  }
<?php endif; ?>
<?php if ($onglets->getCurrentCepage()->getConfig()->hasMinQuantite()) : ?>
    var total_non_negociant = parseFloat($('#appellation_total_volume').val());
    $("#col_recolte_totale .vente_raisins .acheteur").each(function() 
    {
      total_non_negociant -= parseFloat($(this).val());
    });
    var min = total_non_negociant * <?php echo $onglets->getCurrentCepage()->getConfig()->min_quantite ?>;
    var max = total_non_negociant * <?php echo $onglets->getCurrentCepage()->getConfig()->max_quantite ?>;
    
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
  if (parseFloat($('#recolte_cave_particuliere').val()) == 0 && parseFloat($('#appellation_total_cave').val()) > 0)
    rebeche_ratio_respected = false;
  if (!rebeche_ratio_respected) {
    $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_dest_rebeches')); ?></p>');
    openPopup($('#popup_msg_erreur'), 0);
    return false;
  }

    if (parseFloat($('#detail_vol_total_recolte').val()) < min) {
        $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_min_quantite')); ?></p>');
        openPopup($('#popup_msg_erreur'), 0);
        return false;
    }

    if (parseFloat($('#detail_vol_total_recolte').val()) > max) {
        $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_max_quantite')); ?></p>');
        openPopup($('#popup_msg_erreur'), 0);
        return false;
    }
<?php endif; ?>
<?php if ($onglets->getCurrentCepage()->getConfig()->hasDenomination() && $onglets->getCurrentCepage()->getConfig()->hasVtsgn()): ?>
    var couples_denomination_mention_existant = <?php echo json_encode($onglets->getCurrentCepage()->getArrayVtSgnDenomination(array($form->getObject()->getKey()))->getRawValue()) ?>;
    var denomination_val = $('.col_recolte.col_active .col_cont p.denomination input').val();
    var mention_val = $('.col_recolte.col_active .col_cont p.mention select').val();
    
    var couples_denomination_mention_is_bad = false;
    for(var couples_item_key in couples_denomination_mention_existant) {
        couples_item = couples_denomination_mention_existant[couples_item_key];
        couples_denomination_mention_is_bad = (couples_item.denomination == denomination_val) && (couples_item.vtsgn == mention_val);

        if (couples_denomination_mention_is_bad) {
            break;
        }
    }
        
    if (couples_denomination_mention_is_bad) {
        $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_unique_mention_denomination')); ?></p>');
        openPopup($('#popup_msg_erreur'), 0);
        return false;
    }
<?php elseif($onglets->getCurrentCepage()->getConfig()->hasDenomination() && !$onglets->getCurrentCepage()->getConfig()->hasVtsgn()): ?>
        var array_denomination_existant = <?php echo json_encode($onglets->getCurrentCepage()->getArrayDenomination(array($form->getObject()->getKey()))->getRawValue()) ?>;
        var denomination_val = $('.col_recolte.col_active .col_cont p.denomination input').val();
        
        for(var couples_item_key in array_denomination_existant) {
            var denomination_is_bad = false;
            couples_item = array_denomination_existant[couples_item_key];
            if(couples_item.denomination == denomination_val){
                denomination_is_bad = true;
                break;
            }
        }

        if (denomination_is_bad) {
            $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_unique_denomination')); ?></p>');
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