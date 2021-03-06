<?php include_partial('dr/etapes', array('etape' => 2, 'dr' => $dr)) ?>
<?php include_partial('dr/actions', array('etape' => 2, 'help_popup_action' => $help_popup_action)) ?>

<?php include_partial('global/errorMessages', array('form' => $form)); ?>

<script type="text/javascript">
    url_ajax = '<?php echo url_for('dr_repartition_acheteurs', array('id' => $dr->_id)) ?>';
</script>

<!-- #principal -->
<form id="principal" action="<?php echo url_for('dr_repartition', $dr) ?>" method="post">
    <?php echo $form->renderHiddenFields(); ?>
    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#exploitation_acheteurs">Répartition de la récolte</a></li>
    </ul>

    <!-- #  application_dr -->
    <div id="application_dr" class="clearfix">
        <!-- #exploitation_acheteurs -->
        <div id="exploitation_acheteurs">
            <p class="intro_declaration"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_exploitation_acheteurs'); ?>
            <?php if($form->getObject()->getCouchdbDocument()->hasDateDepotMairie()):  ?>
                &nbsp; &nbsp;
                <?php echo $form['date_depot_mairie']->renderLabel(); ?>
                <?php echo $form['date_depot_mairie']->render(array('class' => "datepicker datepickerOpenOnLoad")); ?>
        <?php endif; ?>
            </p>
            <script type="text/javascript">
                var_liste_acheteurs = <?php echo ListAcheteursConfig::getNegocesJson(null, $acheteurs_negociant_using->getRawValue()) ?>;
                var_liste_acheteurs_using = <?php echo ListAcheteursConfig::getNegocesJson($acheteurs_negociant_using->getRawValue(), null) ?>;
                var_liste_caves = <?php echo ListAcheteursConfig::getCooperativesJson(null, $acheteurs_cave_using->getRawValue()) ?>;
                var_liste_caves_using = <?php echo ListAcheteursConfig::getCooperativesJson($acheteurs_cave_using->getRawValue(), null) ?>;
                var_liste_acheteurs_mouts = <?php echo ListAcheteursConfig::getMoutsJson(null, $acheteurs_mout_using->getRawValue()) ?>;
                var_liste_acheteurs_mouts_using = <?php echo ListAcheteursConfig::getMoutsJson($acheteurs_mout_using->getRawValue(), null) ?>;
            </script>
            <div id="vol_sur_place">
                <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto tables_acheteurs">
                    <?php
                    include_partial('exploitationAcheteursTableHeader', array('image_filename' => 'vol_sur_place.png',
                        'image_alt' => "Volume sur place",
                        'appellations' => $appellations,
                        'rel_help_msg' => 'help_popup_exploitation_acheteur_vol_sur_place'));
                    ?>
                    <tbody>
                        <?php
                        include_partial('exploitationAcheteursTableRowItem', array('nom' => null,
                            'cvi' => null,
                            'commune' => null,
                            'appellations' => $appellations,
                            'form_item' => $form[ExploitationAcheteursForm::FORM_NAME_CAVE_PARTICULIERE],
                            'delete' => false));
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="acheteurs_raisin">

                <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto tables_acheteurs">
                    <?php
                    include_partial('exploitationAcheteursTableHeader', array('image_filename' => 'acheteurs_raisin.png',
                        'image_alt' => "Acheteurs de raisin",
                        'appellations' => $appellations,
                        'rel_help_msg' => 'help_popup_exploitation_acheteur_acheteurs_raisin'));
                    ?>
                    <tbody>
                        <!-- garder cette ligne -->
                        <?php include_partial('exploitationAcheteursTableRowEmtpy', array('appellations' => $appellations)); ?>
                        <!-- fin garder cette ligne -->
                        <?php
                        include_partial('exploitationAcheteursTableRowList', array('acheteurs' => $acheteurs_negociant,
                            'appellations' => $appellations,
                            'form' => $form,
                            'name' => ExploitationAcheteursForm::FORM_NAME_NEGOCES));
                        ?>
                    </tbody>
                </table>

                <div class="btn">
                    <a href="#" class="ajouter"><img src="/images/boutons/btn_ajouter_un_acheteur.png" alt="Ajouter un acheteur" /></a>
                </div>
            </div>

            <div id="ajout_acheteur" class="form_ajout" rel="<?php echo ExploitationAcheteursForm::FORM_NAME_NEGOCES ?>">
                <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto" rel="var_liste_acheteurs">
                    <?php
                    include_partial('exploitationAcheteursTableHeader', array('image_filename' => 'acheteurs_raisin.png',
                        'image_alt' => "Acheteurs de raisin",
                        'appellations' => $appellations));
                    ?>
                    <tbody>
                        <?php include_partial('exploitationAcheteursTableRowItemAdd', array('appellations' => $appellations)); ?>
                    </tbody>
                </table>
                <div class="btn">
                    <a href="#" class="valider"><img src="/images/boutons/btn_valider_acheteur.png" alt="Ajouter cet acheteur" /></a>
                    <a href="#" class="annuler"><img src="/images/boutons/btn_annuler_ajout.png" alt="Annuler" /></a>
                </div>
            </div>

            <div id="caves_cooperatives">

                <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto tables_acheteurs">
                    <?php
                    include_partial('exploitationAcheteursTableHeader', array('image_filename' => 'caves_cooperatives.png',
                        'image_alt' => "Caves coopératives",
                        'appellations' => $appellations,
                        'rel_help_msg' => 'help_popup_exploitation_acheteur_caves_cooperatives'));
                    ?>
                    <tbody>
                        <!-- garder cette ligne -->
                        <?php include_partial('exploitationAcheteursTableRowEmtpy', array('appellations' => $appellations)); ?>
                        <!-- fin garder cette ligne -->
                        <?php
                        include_partial('exploitationAcheteursTableRowList', array('acheteurs' => $acheteurs_cave,
                            'appellations' => $appellations,
                            'form' => $form,
                            'name' => ExploitationAcheteursForm::FORM_NAME_COOPERATIVES));
                        ?>
                    </tbody>
                </table>

                <div class="btn">
                    <a href="#" class="ajouter"><img src="/images/boutons/btn_ajouter_une_cave.png" alt="Ajouter une cave" /></a>
                </div>
            </div>

            <div id="ajout_cave" class="form_ajout" rel="<?php echo ExploitationAcheteursForm::FORM_NAME_COOPERATIVES ?>">
                <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto" rel="var_liste_caves">
                    <?php
                    include_partial('exploitationAcheteursTableHeader', array('image_filename' => 'caves_cooperatives.png',
                        'image_alt' => "Caves coopératives",
                        'appellations' => $appellations));
                    ?>
                    <tbody>
                        <?php include_partial('exploitationAcheteursTableRowItemAdd', array('appellations' => $appellations)); ?>
                    </tbody>
                </table>
                <div class="btn">
                    <a href="#" class="valider"><img src="/images/boutons/btn_valider_acheteur.png" alt="Ajouter cette cave" /></a>
                    <a href="#" class="annuler"><img src="/images/boutons/btn_annuler_ajout.png" alt="Annuler" /></a>
                </div>
            </div>

            <div id="acheteurs_mouts">

                <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto tables_acheteurs">
                    <?php
                    include_partial('exploitationAcheteursTableHeader', array('image_filename' => 'acheteurs_mouts_cremant_alsace.png',
                        'image_alt' => "Acheteurs de mouts Crémant d'Alsace",
                        'appellations' => $appellations,
                        'mout' => true,
                        'rel_help_msg' => 'help_popup_exploitation_acheteur_acheteurs_mouts'));
                    ?>
                    <tbody>
                        <!-- garder cette ligne -->
                        <?php include_partial('exploitationAcheteursTableRowEmtpy', array('appellations' => $appellations)); ?>
                        <!-- fin garder cette ligne -->
                        <?php
                        include_partial('exploitationAcheteursTableRowList', array('acheteurs' => $acheteurs_mout,
                            'appellations' => $appellations,
                            'form' => $form,
                            'name' => ExploitationAcheteursForm::FORM_NAME_MOUTS));
                        ?>
                    </tbody>
                </table>

                <div class="btn">
                    <a href="#" class="ajouter"><img src="/images/boutons/btn_ajouter_un_acheteur.png" alt="Ajouter un acheteur" /></a>
                </div>
            </div>

            <div id="ajout_acheteur_mout" class="form_ajout" rel="<?php echo ExploitationAcheteursForm::FORM_NAME_MOUTS ?>">
                <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto" rel="var_liste_acheteurs_mouts">
                    <?php
                    include_partial('exploitationAcheteursTableHeader', array('image_filename' => 'acheteurs_mouts_cremant_alsace.png',
                        'image_alt' => "Acheteurs de mouts Crémant d'Alsace",
                        'appellations' => $appellations,
                        'mout' => true));
                    ?>
                    <tbody>
                        <?php include_partial('exploitationAcheteursTableRowItemAdd', array('appellations' => $appellations,
                            'mout' => true)); ?>
                    </tbody>
                </table>
                <div class="btn">
                    <a href="#" class="valider"><img src="/images/boutons/btn_valider_acheteur.png" alt="Ajouter cet acheteur" /></a>
                    <a href="#" class="annuler"><img src="/images/boutons/btn_annuler_ajout.png" alt="Annuler" /></a>
                </div>
            </div>

        </div>

    </div>
    <!-- fin #application_dr -->

    <div id="popup_msg_erreur" class="popup_ajout" title="Erreur !">
        <p><?php include_partial('global/message', array('id'=>'err_exploitation_acheteurs_popup_no_required')); ?></p>
        <p style="color: #666; padding-bottom: 20px; margin-top: 15px;">Si vous n'avez que des jeunes vignes à déclarer,&nbsp;
            <a style="color: #666;" href="<?php echo url_for('dr_no_recolte', $dr) ?>">
                cliquez ici
            </a>
        </p>
    </div>

    <?php include_partial('dr/boutons', array('display' => array('precedent', 'suivant'))) ?>

</form>
<!-- fin #principal -->
