<div id="popup_ajout_appelation" class="popup_ajout" title="Ajouter une appellation">
    <?php include_partial('dr_recolte/ajoutAppellationForm', array('produit' => $produit,
        'form' => $form_appellation)) ?>

</div>

<?php if ($produit->getAppellation()->getConfig()->hasManyLieu()): ?>
    <div id="popup_ajout_lieu" class="popup_ajout" title="Ajouter un lieu dit">
        <?php include_partial('dr_recolte/ajoutLieuForm', array('produit' => $produit,
                'form' => $form_lieu,
                'url' => $url_lieu)) ?>
    </div>
<?php endif; ?>
