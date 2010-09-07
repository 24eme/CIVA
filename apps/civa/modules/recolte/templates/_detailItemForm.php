<div class="col_recolte col_active">
    <form id="form_detail" action="<?php echo ($is_new) ? url_for($onglets->getUrl('recolte_add')->getRawValue()) : url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>" method="post">
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

        <div class="vente_raisins">
            <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_NEGOCES])); ?>
            <a href="#" class="ajout ajout_acheteur">Acheteur</a>
        </div>

        <div class="caves">
            <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_COOPERATIVES])); ?>
            <a href="#" class="ajout ajout_cave">Cave</a>
        </div>

        <?php if (isset($form[RecolteForm::FORM_NAME_MOUTS])): ?>
        <div class="mouts">
            <?php include_partial('formAcheteurs', array('form_acheteurs' => $form[RecolteForm::FORM_NAME_MOUTS])); ?>
            <a href="#" class="ajout ajout_mout">Ajouter mout</a>
        </div>
        <?php endif; ?>

        <p class="vol_place <?php echo ($form['cave_particuliere']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
            <?php echo $form['cave_particuliere']->render(array('class' => 'num cave volume')) ?>
        </p>
        <p class="vol_total_recolte"><input type="text" id="detail_vol_total_recolte" class="num total readonly" readonly="readonly" value="<?php echo $detail->volume ?>" /></p>
        <?php if ($detail->hasRendementCepage()): ?>
        <ul class="vol_revendique_dplc">
	   <input type="hidden" id="detail_max_volume" value="<?php echo $detail->getVolumeMax(); ?>"/>
	   <input type="hidden" id="detail_rendement" value="<?php echo $detail->getRendementCepage(); ?>"/>
            <li><input id="detail_volume_revendique" type="text" class="revendique num readonly" readonly="readonly" value="<?php echo $detail->volume_revendique ?>" /></li>
            <li><input id="detail_volume_dplc" type="text" class="dplc num readonly" readonly="readonly" value="<?php echo $detail->volume_dplc ?>" /></li>
        </ul>
        <?php endif; ?>
    </div>

    <div class="col_btn">
        <a href="<?php echo url_for($onglets->getUrl('recolte')->getRawValue()); ?>" class="annuler_tmp"><img src="/images/boutons/btn_annuler_col_cepage.png" alt="Annuler" /></a>
<script><!--
function valider_can_submit() 
{
<?php if ($onglets->getCurrentCepage()->getConfig()->hasSuperficie()) : ?>
  if (!document.getElementById('recolte_superficie').value) {
    $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_no_superficie')); ?></p>');
    openPopup($('#popup_msg_erreur'), 0);
    return false;
  }
<?php endif; ?>
<?php if ($onglets->getCurrentCepage()->getConfig()->hasMinQuantite()) : ?>
    var min = parseFloat($('#appellation_total_volume').val()) * <?php echo $onglets->getCurrentCepage()->getConfig()->min_quantite ?>;
    if (parseFloat($('#detail_vol_total_recolte').val()) < min) {
    $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_min_quantite')); ?></p>');
    openPopup($('#popup_msg_erreur'), 0);
    return false;
  }
<?php endif; ?>
<?php if ($onglets->getCurrentCepage()->getConfig()->hasDenomination() && $onglets->getCurrentCepage()->getConfig()->hasVtsgn()): ?>
    var denomination_is_bad = false;
    var mention_is_bad = false;

    <?php if ($onglets->getCurrentCepage()->getConfig()->hasDenomination()): ?>
        var denomination_is_bad = ($('.col_recolte').find('.col_cont p.denomination input[value="'+$('.col_recolte.col_active .col_cont p.denomination input').val()+'"]').length > 1);
    <?php endif; ?>

    <?php if ($onglets->getCurrentCepage()->getConfig()->hasVtsgn()): ?>
        var mention_is_bad = ($('.col_recolte').find('.col_cont p.mention select[value="'+$('.col_recolte.col_active .col_cont p.mention select').val()+'"]').length > 1);
    <?php endif; ?>
        
    if (denomination_is_bad && mention_is_bad) {
        $('#popup_msg_erreur').html('<p><?php include_partial('global/message', array('id'=>'err_dr_popup_unique_mention_denomination')); ?></p>');
        openPopup($('#popup_msg_erreur'), 0);
        return false;
    }
<?php endif; ?>
  document.getElementById('form_detail').submit();
}
--></script>
        <a href="#" class="valider_tmp"><img src="/images/boutons/btn_valider_col_cepage.png" alt="Valider" onclick="valider_can_submit(); return false;" /></a>
    </div>
    </form>
</div>
<div id="popup_msg_erreur" title="Erreur !">
</div>
