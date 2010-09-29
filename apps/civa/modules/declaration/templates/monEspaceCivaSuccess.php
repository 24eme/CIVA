<?php include_partial('global/actions', array('etape' => 0, 'help_popup_action'=>$help_popup_action)) ?>

<!-- #principal -->
<form id="principal" action="<?php echo url_for('@mon_espace_civa') ?>" method="post">

    <h2 class="titre_principal">Mon espace CIVA</h2>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <?php if($sf_user->hasFlash('mdp_modif')) { ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('mdp_modif'); ?></p>
        <?php } ?>

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
            <?php if($dr_non_editable==1): ?>
            <h3 class="titre_section">Déclaration de l'année <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide">Test message d'aide</a></h3>
                <div class="contenu_section">
                    <p class="intro"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_dr_non_editable'); ?></p>
                    <div class="ligne_form ligne_btn">
                        <?php echo link_to('<img src="../images/boutons/btn_visualiser.png" alt="" class="btn" />', '@visualisation?annee='.$sf_user->getCampagne()); ?>
                    </div>
                </div>
            <?php elseif($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_BROUILLON)): ?>
            <h3 class="titre_section">Déclaration de l'année<a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide">Test message d'aide</a></h3>
            <div class="contenu_section">
                <p class="intro">Vous souhaitez faire une nouvelle déclaration :</p>
                <?php if ($declaration): ?>
                <div class="ligne_form">
                    <input type="radio" id="type_declaration_brouillon" name="dr[type_declaration]" value="brouillon" checked="checked" />
                    <label for="type_declaration_brouillon">Continuer ma déclaration</label>
                </div>
                <div class="ligne_form">
                    <input type="radio" id="type_declaration_suppr" name="dr[type_declaration]" value="supprimer" />
                    <label for="type_declaration_suppr">Supprimer ma déclaration <?php echo $sf_user->getCampagne() ?> en cours</label>
                </div>
                <div class="ligne_form ligne_btn">
                    <input type="image" class="btn" name="boutons[valider]" id="mon_espace_civa_valider" src="../images/boutons/btn_valider.png" alt="Valider" />
                </div>
                <?php else: ?>
                <div class="ligne_form">
                    <input type="radio" id="type_declaration_vierge" name="dr[type_declaration]" value="vierge" checked="checked" />
                    <label for="type_declaration_vierge">A partir d'une déclaration vierge</label>
                </div>
                    <?php if (count($campagnes) > 0): ?>
                <div class="ligne_form">
                    <input type="radio" id="type_declaration_precedente" name="dr[type_declaration]" value="precedente" />
                    <label for="type_declaration_precedente">A partir d'une précédente déclaration</label>
                </div>
                <div class="ligne_form ligne_btn">
                    <select id="liste_precedentes_declarations" name="dr[liste_precedentes_declarations]">
                                <?php foreach ($campagnes as $id => $campagne): ?>
                        <option value="<?php echo $campagne ?>">DR <?php echo $campagne ?></option>
                                <?php endforeach; ?>
                    </select>

                    <input type="image" name="boutons[valider]"  class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
                </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php elseif($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_VALIDE)): ?>
                <h3 class="titre_section">Déclaration de l'année<a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide">Test message d'aide</a></h3>
                <div class="contenu_section">
                    <p class="intro"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_dr_validee'); ?></p>
                    <div class="ligne_form ligne_btn">
                        <?php echo link_to('<img src="../images/boutons/btn_visualiser.png" alt="" class="btn" />', '@visualisation?annee='.$sf_user->getCampagne()); ?>
                    </div>
                </div>
            <?php endif; ?>
            <br />
            <?php if($has_no_assices){ ?>
            <h3 class="titre_section">Gamma <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_gamma" title="Message aide">Test message d'aide</a></h3>
            <div class="contenu_section">
                <p class="intro"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_gamma'); ?></p>

            </div>
            <?php } ?>
        </div>
        <!-- fin #nouvelle_declaration -->

        <!-- #precedentes_declarations -->
        <div id="precedentes_declarations">
            <h3 class="titre_section">Visualiser mes DRécolte <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_visualiser" title="Message aide">Test message d'aide</a></h3>
            <div class="contenu_section">

                <ul class="bloc_vert">
                    <li>
                        <a href="#">Années précédentes</a>
                        <?php if (count($campagnes) > 0): ?>
                        <ul class="declarations">
                            <?php foreach ($campagnes as $id => $campagne): ?>
                            <!--<li>
                                <a href="#"><?php echo $campagne ?></a>
                                <ul>-->
                                    <li><?php echo link_to($campagne, '@visualisation?annee='.$campagne); ?></li>
                               <!-- </ul>
                            </li>-->
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                </ul>

            </div>
        </div>
        <!-- fin #precedentes_declarations -->
    </div>
    <!-- fin #application_dr -->

</form>
<!-- fin #principal -->