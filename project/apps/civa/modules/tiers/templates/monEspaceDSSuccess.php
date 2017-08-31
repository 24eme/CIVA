<?php include_partial('tiers/title') ?>

<div id="application_dr" class="mon_espace_civa clearfix">
    <?php include_partial('tiers/onglets', array('active' => 'stock_'.$type_ds, 'compte' => $compte, 'blocs' => $blocs)) ?>

    <div id="espace_alsace_recolte" class="contenu clearfix">

        <?php $etablissements = DSCivaClient::getInstance()->getEtablissements($compte->getSociete(), $type_ds); ?>
        <?php if(count($etablissements) > 1): ?>
        <div style="margin-bottom: 20px;">
            <label for="select_choix_etablissement">Établissement :</label>
            <select style="width: 400px;" onchange="document.location = this.value" id="select_choix_etablissement">
            <?php foreach($etablissements as $e): ?>
                <option value="<?php echo url_for('mon_espace_civa_ds', array('sf_subject' => $e, 'type' => $type_ds)) ?>" <?php if($e->_id == $etablissement->_id): ?>selected="selected"<?php endif; ?>><?php echo $e->nom ?></option>
            <?php endforeach; ?>
            </select>
            <button>Changer</button>
        </div>
        <?php endif; ?>

        <?php if($sf_user->hasFlash('confirmation')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
        <?php endif; ?>
        <?php include_component('ds', 'monEspace', array('type_ds' => $type_ds, 'etablissement' => $etablissement)) ?>
        <?php include_component('ds', 'monEspaceColonne', array('type_ds' => $type_ds, 'etablissement' => $etablissement)) ?>
    </div>
</div>
