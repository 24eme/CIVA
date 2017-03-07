<div id="gestion_grands_crus" style="margin-right: 30px;">
    <p class="intro_declaration"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_exploitation_lieu_'.strtolower($appellation->getConfig()->getKey())); ?></p>

    <h2 class="titre_section"><?php echo $appellation->getLibelle() ?></h2>
    <div class="contenu_section">
        <?php if ($appellation->mention->hasLieux()): ?>
        <p class="txt_gris"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_exploitation_lieu_txt_gris_'.strtolower($appellation->getConfig()->getKey())); ?> <span style="margin-right: 25px; margin-top: -10px; float:right; text-align: center">Déclarer des VT / SGN<br /><small style="font-size: 12px; font-style: italic;">(en cochant les cases)</small></span></p>
            <ul id="liste_grands_crus">
                <?php foreach ($appellation->mention->getLieux() as $k => $l) : ?>
                    <li><?php echo $l->getLibelle(); ?>
                        <?php if($appellation->hasDetailsInLieu($k)): ?>
                            <a onclick="alert('Ce lieu-dit ne peut pas être supprimé car il y a des colonnes saisies'); return false;" style="position: relative; float: right;  top: 2px; right: 0; margin-left: 70px; opacity: 0.5;" class="supprimer" href="#"><img alt="Supprimer" src="/images/pictos/pi_supprimer.png"></a>
                        <?php else: ?>
                            <a style="position: relative; float: right;  top: 2px; right: 0; margin-left: 70px;" class="supprimer" href="<?php echo url_for('dr_repartition_lieu_delete', array('hash' => $appellation->getHash(),  'lieu' => $k, 'id' => $dr->_id)); ?>"><img alt="Supprimer" src="/images/pictos/pi_supprimer.png"></a>
                        <?php endif; ?>
                        <label style="font-weight: normal; float: right; margin-top: 2px;"><?php echo $form['lieux_vtsgn'][$k]->render(); ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
        <?php endif; ?>

        <?php echo $form->renderHiddenFields(); ?>

        <p class="txt_gris">
            <?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_exploitation_lieu_txt_consigne_'.strtolower($appellation->getConfig()->getKey())); ?>
        </p>

        <?php include_partial('global/errorMessages', array('form' => $form)); ?>

        <div class="ligne_form <?php echo ($form['ajout']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
            <label for="champ_ajout_lieu_dit"><?php
            echo $form['ajout']->renderLabel(acCouchdbManager::getClient('Messages')->getMessage('intro_exploitation_lieu_txt_label_'.strtolower($appellation->getConfig()->getKey()))); ?></label>
            <?php echo $form['ajout']->render(); ?>
        </div>
        <div class="ligne_form_btn">
            <input name="<?php echo $form->getName() ?>" type="image" alt="valider" src="/images/boutons/btn_valider.png" class="btn">
        </div>
   </div>
</div>
