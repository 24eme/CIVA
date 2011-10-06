<div id="gestion_grands_crus" style="margin-right: 30px;">
    <p class="intro_declaration"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_exploitation_lieu'); ?></p>

    <h2 class="titre_section"><?php echo $appellation->getLibelle() ?></h2>
    <div class="contenu_section">
        <p class="txt_gris"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_exploitation_lieu_txt_gris'); ?></p>
        <?php if (count($form->getOption('lieux', array()))) : ?>
            <ul id="liste_grands_crus">
                <?php foreach ($form->getOption('lieux', array()) as $k => $l) : ?>
                    <li><?php echo $l; ?> <a class="supprimer" href="<?php echo url_for('@exploitation_lieu_delete?appellation='.$appellation->getConfig()->appellation.'&lieu=' . $k); ?>"><img alt="Supprimer" src="/images/pictos/pi_supprimer.png"></a></li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>

        <?php endif; ?>

        <?php echo $form->renderHiddenFields(); ?>

        <p class="txt_gris">Sélectionnez un lieu-dit dans la liste suivante :</p>

        <?php include_partial('global/errorMessages', array('form' => $form)); ?>

        <div class="ligne_form <?php echo ($form['lieu']->hasError()) ? sfConfig::get('app_css_class_field_error') : null ?>">
            <label for="champ_ajout_lieu_dit"><?php echo $form['lieu']->renderLabel("Ajoutez un lieu-dit :"); ?></label>
            <?php echo $form['lieu']->render(); ?>
            <input name="<?php echo $form->getName() ?>" type="image" alt="valider" src="../images/boutons/btn_valider.png" class="btn">
        </div>

    </div>
</div>