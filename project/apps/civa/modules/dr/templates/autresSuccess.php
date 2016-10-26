<?php include_partial('dr/etapes', array('etape' => 3, 'dr' => $dr)) ?>
<?php include_partial('dr/actions', array('etape' => 0, 'help_popup_action'=>$help_popup_action)) ?>

<!-- #principal -->
<form id="principal" action="" method="post">
        <?php echo $form->renderHiddenFields(); ?>
        <ul id="onglets_majeurs" class="clearfix">
                <li class="ui-tabs-selected"><a href="#exploitation_autres">Autres</a></li>
        </ul>

        <!-- #application_dr -->
        <div id="application_dr" class="clearfix">

                <!-- #exploitation_autres -->
                <div id="exploitation_autres">
                    <p class="intro_declaration"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_exploitation_autres'); ?></p>
                    <?php include_partial('global/errorMessages', array('form' => $form)); ?>

                        <div id="jeunes_vignes">
                                <h2 class="titre_section">Jeunes Vignes sans production <a href="" class="msg_aide" rel="help_popup_autres_jv" title="Message aide"></a></h2>
                                <div class="contenu_section">
                                        <div class="bloc_vert" action="" method="post">
                                                <div class="ligne_form <?php echo ($form['jeunes_vignes']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
                                                        <?php echo $form['jeunes_vignes']->render(array('class' => 'num num_light num_float', 'style' => 'padding: 0 5px;')) ?>
                                                        <?php echo $form['jeunes_vignes']->renderLabel() ?>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
                <!-- fin #exploitation_autres -->

        </div>
        <!-- fin #application_dr -->

        <?php include_partial('dr/boutons', array('display' => array('precedent','suivant'), 'dr' => $dr)) ?>

</form>
<!-- fin #principal -->
