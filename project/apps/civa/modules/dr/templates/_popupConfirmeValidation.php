<div id="popup_confirme_validation" class="popup_ajout popup_confirme" title="Validation de votre DR">
    <form method="post" action="">
        <p>
            Une fois votre déclaration validée, vous ne pourrez plus la modifier. <br /><br />
            Confirmez-vous la validation de votre déclaration de récolte ?<br />
        </p>
        <?php $need_acheteurs_autorisation = !$dr->hasDateDepotMairie() && DRClient::getInstance()->hasImport($dr->cvi, $dr->campagne); ?>
        <?php $need_ava_autorisation = !$dr->hasDateDepotMairie() && $dr->hasVolumeSurPlace(); ?>
        <?php if($need_acheteurs_autorisation || $need_ava_autorisation): ?>
            <div style="margin-top: 15px;" class="ligne_form">
            <label>Autorisations de transmission de votre Déclaration de Récolte :</label>
            </div>
            <?php if($need_acheteurs_autorisation): ?>
            <div class="ligne_form">
                <input name="autorisations[]" id="checkbox_partage_acheteurs" checked="checked" style="float:left; margin-right: 8px; margin-left: 0px; margin-top: 3px;" type="checkbox" value="<?php echo DRClient::AUTORISATION_ACHETEURS ?>" />
                <label style="margin-left: 22px;" for="checkbox_partage_acheteurs">À vos acheteurs</label>
            </div>
            <?php endif; ?>
            <?php if($need_ava_autorisation): ?>
            <div class="ligne_form">
                <input name="autorisations[]" id="checkbox_partage_ava" checked="checked" style="float:left; margin-right: 8px; margin-left: 0px; margin-top: 3px;" type="checkbox" value="<?php echo DRClient::AUTORISATION_AVA ?>" />
                <label style="margin-left: 22px;" for="checkbox_partage_ava">À l'AVA pour télédéclarer votre Déclaration de Revendication</label>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($formDatesModification)): ?>
            <div id="administration_validation">
                <h2 class="titre_section">Administration</h2>
                <div class="contenu_section">
                    <div class="bloc_gris presentation">
                    <div class="bloc_form">
                            <?php echo $formDatesModification->renderGlobalErrors(); ?>
                            <?php echo $formDatesModification->renderHiddenFields(); ?>
                            <div class="ligne_form">
                                <?php echo $formDatesModification['date']->renderLabel(null, array('style' => 'display: inline-block;')); ?>
                                <?php echo $formDatesModification['date']->renderError(); ?>
                                <?php echo $formDatesModification['date']->render(array('class' => "datepicker")); ?>
                            </div>
                            <?php if (isset($formDatesModification['compte'])): ?>
                            <div class="ligne_form">
                                <?php echo $formDatesModification['compte']->renderLabel(null, array('style' => 'display: inline-block;')); ?>
                                <?php echo $formDatesModification['compte']->renderError(); ?>
                                <?php echo $formDatesModification['compte']->render(array('class' => "datepicker")); ?>
                            </div>
                            <?php else: ?>
                            <div class="ligne_form">
                                <label style="display: inline-block;">Validée par</label> <?php echo $validation_compte_id; ?>
                            </div>    
                            <?php endif; ?>
                    </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div id="btns">
            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
            <input type="image" src="/images/boutons/btn_valider.png" alt="Valider votre déclaration" name="boutons[next]" id="valideDR" class="valideDR_OK" />
        </div>
    </form>
</div>
