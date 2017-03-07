<?php include_partial('dr/etapes', array('etape' => 3, 'dr' => $declaration)) ?>
<?php include_partial('dr/actions', array('etape' => 3, 'help_popup_action' => $help_popup_action)) ?>

<?php if ($sf_user->hasFlash('msg_info')): ?>
    <p class="message"><?php echo $sf_user->getFlash('msg_info'); ?></p>
<?php endif; ?>

<p class="intro_declaration_recolte"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_declaration_recolte'); ?></p>

<?php include_partial('global/errorMessages', array('form' => $form_detail)); ?>

<?php include_partial('ongletsAppellations', array(
    'declaration' => $declaration,
    'appellations' => $appellations,
    'produit' => $produit,
)); ?>
<div id="application_dr" class="clearfix">
    <div id="gestion_recolte" class="clearfix gestion_recolte_donnees">

        <?php include_partial('messageRepli', array('produit' => $produit)); ?>
        <?php
        include_partial('ongletsCepages', array('declaration' => $declaration,
            'nb_details_current' => $nb_details_current,
            'produit' => $produit,
            'recapitulatif' => false));
        ?>

        <div id="donnees_recolte_sepage" class="clearfix <?php echo ($produit->getLieu()->getConfig()->hasManyCouleur() && count($produit->getLieu()->getConfig()->getCouleurs()) > 1) ? "deux_totaux" : "" ?>">

            <?php
            include_partial('detailHeader', array('acheteurs' => $acheteurs,
                                                  'has_acheteurs_mout' => $has_acheteurs_mout,
                                                  'produit' => $produit))
            ?>

            <?php
            include_partial('detailList', array(
                'etablissement' => $etablissement,
                'produit' => $produit,
                'details' => $details,
                'detail_key' => $detail_key,
                'detail_action_mode' => $detail_action_mode,
                'form' => $form_detail,
                'acheteurs' => $acheteurs,
                'has_acheteurs_mout' => $has_acheteurs_mout))
            ?>
            <?php
            include_partial('totalCepage', array('cepage' => $produit,
                'produit' => $produit,
                'acheteurs' => $acheteurs,
                'has_acheteurs_mout' => $has_acheteurs_mout));
            ?>
            <ul id="btn_cepage" class="btn_prev_suiv clearfix">
                <?php if ($produit->getConfig()->getPreviousSister()): ?>
                    <li class="prec"><a href="<?php echo url_for('dr_recolte_produit', array('sf_subject' => $produit, 'hash' => HashMapper::inverse($produit->getConfig()->getPreviousSister()->getHash()))) ?>" class="btn_recolte_can_be_inactif"><img src="/images/boutons/btn_passer_cepage_prec.png" alt="Passer au cépage précédent" /></a></li>
                <?php endif; ?>
                <?php if ($produit->getConfig()->getNextSister() && !$produit->getConfig()->getNextSister()->exist('attributs/no_dr')): ?>
                    <li class="suiv"><a href="<?php echo url_for('dr_recolte_produit', array('sf_subject' => $produit, 'hash' => HashMapper::inverse( $produit->getConfig()->getNextSister()->getHash()))) ?>" class="btn_recolte_can_be_inactif"><img src="/images/boutons/btn_passer_cepage_suiv.png" alt="Passer au cépage suivant" /></a></li>
                <?php endif; ?>
            </ul>
        </div>

        <?php if ($produit->getLieu()->getConfig()->hasManyCouleur()): ?>
            <?php foreach ($produit->getLieu()->getCouleurs() as $couleur): ?>
                <?php if ($couleur->getKey() == ($produit->getCouleur()->getKey())): ?>
                    <?php
                    include_partial('totalCouleur', array('couleur' => $couleur,
                        'produit' => $produit,
                        'acheteurs' => $acheteurs,
                        'has_acheteurs_mout' => $has_acheteurs_mout))
                    ?>
                <?php else: ?>
                    <?php
                    include_partial('totalCouleurAlternatif', array('couleur' => $couleur,
                        'produit' => $produit,
                        'acheteurs' => $acheteurs,
                        'has_acheteurs_mout' => $has_acheteurs_mout))
                    ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php elseif (!$produit->getLieu()->getConfig()->hasManyCouleur()): ?>
            <?php
            echo include_partial('totalAppellation', array('lieu' => $produit->getLieu(),
                'produit' => $produit,
                'acheteurs' => $acheteurs,
                'has_acheteurs_mout' => $has_acheteurs_mout))
            ?>
        <?php endif; ?>
    </div>

    <?php include_partial('boutonAppellation', array('produit' => $produit)) ?>

</div>
<!-- fin #application_dr -->

<?php include_partial('boutons', array('dr' => $declaration)) ?>

<!--</form>-->
<!-- fin #principal -->
<script type="text/javascript">
    var_liste_acheteurs = <?php echo ListAcheteursConfig::getNegocesJson(null, $acheteurs->negoces->toArray()->getRawValue()) ?>;
    var_liste_acheteurs_using = <?php echo ListAcheteursConfig::getNegocesJson($acheteurs->negoces->toArray()->getRawValue(), null) ?>;
    var_liste_caves = <?php echo ListAcheteursConfig::getCooperativesJson(null, $acheteurs->cooperatives->toArray()->getRawValue()) ?>;
    var_liste_caves_using = <?php echo ListAcheteursConfig::getCooperativesJson($acheteurs->cooperatives->toArray()->getRawValue(), null) ?>;
    var_liste_acheteurs_mouts = <?php echo ListAcheteursConfig::getMoutsJson(null, $acheteurs->mouts->toArray()->getRawValue()) ?>;
    var_liste_acheteurs_mouts_using = <?php echo ListAcheteursConfig::getMoutsJson($acheteurs->mouts->toArray()->getRawValue(), null) ?>;
    var_config_popup_ajout_motif = { ajax: true , auto_open: false};
<?php if ($sf_user->hasFlash('open_popup_ajout_motif')): ?>
        var_config_popup_ajout_motif.auto_open = true;
        var_config_popup_ajout_motif.auto_open_url = '<?php echo url_for('dr_recolte_motif_non_recolte', array('id' => $declaration->_id, 'hash'=> $produit->getHash(), 'detail_key' => $sf_user->getFlash('open_popup_ajout_motif'))) ?>';
<?php endif; ?>
</script>

<?php
include_partial('popupAjoutOnglets', array(
    'produit' => $produit,
    'form_appellation' => $form_ajout_appellation,
    'form_lieu' => $form_ajout_lieu,
    'url_lieu' => $url_ajout_lieu))
?>

<?php
include_partial('popupAjoutAcheteur', array('id' => 'popup_ajout_acheteur',
    'title' => 'Ajouter un acheteur',
    'action' => url_for('dr_recolte_add_acheteur', array("sf_subject" => $produit->getDocument())),
    'name' => 'negoces',
    'cssclass' => 'vente_raisins'))
?>
<?php
include_partial('popupAjoutAcheteur', array('id' => 'popup_ajout_cave',
    'title' => 'Ajouter une cave',
    'action' => url_for('dr_recolte_add_acheteur', array("sf_subject" => $produit->getDocument())),
    'name' => 'cooperatives',
    'cssclass' => 'caves'))
?>
<?php
include_partial('popupAjoutAcheteur', array('id' => 'popup_ajout_mout',
    'title' => 'Ajouter un acheteur de mout',
    'action' => url_for('dr_recolte_add_acheteur', array("sf_subject" => $produit->getDocument())),
    'name' => 'mouts',
    'cssclass' => 'mouts'))
?>

<?php include_partial('popupMotifNonRecolte') ?>

<?php include_partial('emptyAcheteurs') ?>
<?php include_partial('initRendementsMax', array('dr' => $declaration)) ?>

<?php include_partial('popupDrPrecedentes', array('campagnes' => $campagnes)) ?>

<?php if ($sf_user->hasFlash('flash_message')): ?>
    <?php include_partial('popupRappelLog', array('flash_message' => $sf_user->getFlash('flash_message'))) ?>
<?php endif; ?>
