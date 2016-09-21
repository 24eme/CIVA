<?php use_helper('ds'); ?>
<form id="form_ds" action="<?php echo url_for('ds_init', array('type' => $type_ds, 'sf_subject' => $etablissement)) ?>" method="post">
    <h3 class="titre_section">Déclaration de <?php echo getPeriodeFr($sf_user->getPeriodeDS($type_ds)) ?><a href="" class="msg_aide_ds" rel="help_popup_mon_espace_civa_ma_ds<?php if($type_ds == DSCivaClient::TYPE_DS_NEGOCE): ?>_negoce<?php endif; ?>" title="Message aide"></a></h3>
    <span class="label_type_ds"><?php echo strtoupper($type_ds); ?></span>
    <div class="contenu_section">
        <?php if (isset($ds)): ?>
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
                <input type="image" name="boutons[valider]" id="mon_espace_civa_valider" data-popup-loading="true" class="btn" src="/images/boutons/btn_demarrer.png" alt="Démarrer" />
            </div>
        <?php endif; ?>
        <p class="intro msg_mon_espace_civa"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_ds'); ?></p>
        <?php if($type_ds == DSCivaClient::TYPE_DS_PROPRIETE && !CurrentClient::getCurrent()->isDSDecembre()): ?>
        <a class="btn_majeur btn_petit btn_jaune" href="<?php echo url_for('ds_export_pdf_empty', array('type' => $type_ds)); ?>" style="margin-top: 20px;">Télécharger mon brouillon</a>
        <?php endif; ?>
    </div>
</form>

<div style="display: none" id="popup_loader" title="Génération de la déclaration de Stocks">
    <div class="popup-loading">
    <p>La génération de votre de déclaration de Stocks est en cours.<br />
        <br />
        Les produits qui vous sont proposés sont issus de <strong>votre dernière déclaration de récolte</strong> et/ou de celles de vos apporteurs de raisins, <strong>ainsi que de votre dernière déclaration de Stocks</strong>.<br />
        <br />
        Merci de patienter.<br />
        <br />
        <small>La procédure peut prendre jusqu'à 30 secondes</small>
        </p>
    </div>
</div>
