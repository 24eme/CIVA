<?php use_helper('ds'); ?>
<form id="form_ds" action="<?php echo url_for('ds_init', $ds) ?>" method="post">
    <h3 class="titre_section">Déclaration de <?php echo getPeriodeFr($sf_user->getPeriodeDS()) ?><a href="" class="msg_aide_ds" rel="help_popup_mon_espace_civa_ma_ds" title="Message aide"></a></h3>
    <span class="label_type_ds"><?php echo strtoupper($sf_user->getDeclarantDS()->getTypeDs()); ?></span>
    <div class="contenu_section">    
        <?php if (!$ds->isNew()): ?>
            <p class="intro">Vous souhaitez :</p>
            <div class="ligne_form">
                <input type="radio" id="type_ds_visualisation" name="ds[type_declaration]" value="visualisation"  />
                <label for="type_ds_visualisation">Visualiser ma déclaration en cours</label>
            </div>
            <div class="ligne_form">
                <input type="radio" id="type_ds_brouillon" name="ds[type_declaration]" value="brouillon" checked="checked" />
                <label for="type_ds_brouillon">Continuer ma déclaration</label>
            </div>
            <div class="ligne_form">
                <input type="radio" id="type_ds_suppr" name="ds[type_declaration]" value="supprimer" />
                <label for="type_ds_suppr">Supprimer ma déclaration <?php echo $ds->getAnnee(); ?> en cours</label>
            </div>
        <div class="ligne_form ligne_btn">
           <input type="image" name="boutons[valider]" id="mon_espace_civa_valider" class="btn" src="/images/boutons/btn_valider.png" alt="Valider" />
        </div>
        <?php else: ?>
            <p class="intro">Démarrer une déclaration de stocks</p>
            <div class="ligne_form">
                <input type="radio" id="type_ds_normal" name="ds[type_declaration]" value="ds_normal" checked="checked" />
                <label for="type_ds_normal">Déclaration de Stocks</label>
            </div>
            <div class="ligne_form">
                <input type="radio" id="type_ds_neant" name="ds[type_declaration]" value="ds_neant" />
                <label for="type_ds_neant">Déclaration de Stocks Néant</label>
            </div>
            <div class="ligne_form ligne_btn">
                <input type="image" name="boutons[valider]" id="mon_espace_civa_valider" class="btn" src="/images/boutons/btn_demarrer.png" alt="Démarrer" />
            </div>
        <?php endif; ?>
        <p class="intro msg_mon_espace_civa"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_ds'); ?></p>
    </div>
</form>
<?php if($sf_user->getDeclarant()->isDeclarantStockPropriete()): ?>
<a class="btn_majeur btn_petit btn_jaune" href="<?php echo url_for('ds_export_pdf_empty', array('cvi' => $sf_user->getDeclarantDS()->getIdentifiant())); ?>" style="float: left; margin-top: 20px;">Télécharger mon brouillon</a>
<?php endif; ?>
