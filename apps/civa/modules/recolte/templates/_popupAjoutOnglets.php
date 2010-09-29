<div id="popup_ajout_appelation" class="popup_ajout" title="Ajouter une appellation">
    <?php include_partial('recolte/ajoutAppellationForm', array('onglets' => $onglets,
        'form' => $form_appellation)) ?>
    
</div>

<?php if ($onglets->getCurrentAppellation()->hasManyLieu()): ?>
        <div id="popup_ajout_lieu" class="popup_ajout" title="Ajouter un lieu dit">
    <?php
        include_partial('recolte/ajoutLieuForm', array('onglets' => $onglets,
            'form' => $form_lieu,
            'url' => $url_lieu))
    ?>
            
        </div>
<?php endif; ?>