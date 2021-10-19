<span class="ombre"></span>
<div id="col_scroller" style="<?php if ($produit->getConfig()->hasTotalCepage() && $produit->getLieu()->getConfig()->hasManyCouleur()): ?>width: 291px;<?php endif; ?>">
    <div id="col_scroller_cont" class="clearfix">
        <?php foreach ($produit->detail as $key => $detail): ?>
        <?php if (in_array($detail_action_mode, array('add', 'update')) && $key == $detail_key): ?>
            <?php include_partial('detailItemForm', array(
                'etablissement' => $etablissement,
                'detail' => $detail,
                'produit' => $produit,
                'key' => $key,
                'form' => $form,
                'is_new' => ($detail_action_mode == 'add'))) ?>
        <?php else: ?>
        <?php
                    include_partial('detailItem', array(
                        'etablissement' => $etablissement,
                        'detail' => $detail,
                        'produit' => $produit,
                        'key' => $key,
                        'acheteurs' => $acheteurs,
                        'has_acheteurs_mout' => $has_acheteurs_mout)) ?>
        <?php endif; ?>
<?php endforeach; ?>
<?php if (!$produit->getConfig()->hasOnlyOneDetail() || !count($produit->detail) ) :?>
        <a href="<?php echo url_for('dr_recolte_produit_ajout', array('sf_subject' => $produit, 'hash' => $produit->getHash())) ?>" id="ajout_col" class=""><img src="/images/boutons/btn_ajouter_colonne.png" alt="Ajouter une colonne" /></a>
<?php endif; ?>
    </div>
</div>
<span class="ombre ombre_droite"></span>
