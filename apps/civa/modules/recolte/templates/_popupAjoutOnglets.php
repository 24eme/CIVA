<div id="popup_ajout_appelation" class="popup_ajout" title="Ajouter une appelation">
    <?php include_partial('recolte/ajoutAppellationForm', array('onglets' => $onglets,
        'form' => $form_appellation)) ?>
    <div class="close_btn"><a class="close_popup" href=""><img alt="Fermer la fenetre" src="/images/boutons/btn_fermer.png"></a></div>
</div>

<?php if ($onglets->getCurrentAppellation()->hasManyLieu()): ?>
        <div id="popup_ajout_lieu" class="popup_ajout" title="Ajouter un lieu dit">
    <?php
        include_partial('recolte/ajoutLieuForm', array('onglets' => $onglets,
            'form' => $form_lieu,
            'url' => $url_lieu))
    ?>
            <div class="close_btn"><a class="close_popup" href=""><img alt="Fermer la fenetre" src="/images/boutons/btn_fermer.png"></a></div>
        </div>
<?php endif; ?>