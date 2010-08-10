<div id="colonne_intitules" style="margin-left: 2px;">
    <ul class="denomination_mention">
        <li><?php echo $detail->denomination ?>&nbsp;</li>
        <li><?php echo $detail->vtsgn ?>&nbsp;</li>

    </ul>

    <p class="superficie"><?php echo $detail->superficie ?>&nbsp;</p>

    <div class="vente_raisins">
        <?php include_partial('detailItemAcheteurs', array('title' => "Ventes de Raisins",
                                                             'acheteurs' => $acheteurs_negoce,
                                                             'acheteurs_value' => $detail->getAcheteursValuesWithCvi('negoces'))) ?>
    </div>

    <div class="caves">
        <?php include_partial('detailItemAcheteurs', array('title' => "Caves Coopératives",
                                                             'acheteurs' => $acheteurs_cave,
                                                             'acheteurs_value' => $detail->getAcheteursValuesWithCvi('cooperatives'))) ?>
    </div>

    <?php if ($has_acheteurs_mout): ?>
    <div class="caves">
        <?php include_partial('detailItemAcheteurs', array('title' => "Acheteurs de Mouts",
                                                             'acheteurs' => $acheteurs_mouts,
                                                             'acheteurs_value' => $detail->getAcheteursValuesWithCvi('mouts'))) ?>
    </div>
    <?php endif; ?>


    <p class="vol_place"><?php echo $detail->cave_particuliere ?>&nbsp;</p>

    <p class="vol_total_recolte"><?php echo $detail->volume ?>&nbsp;</p>

    <ul class="vol_revendique_dplc">
        <li><?php echo $detail->volume_revendique ?>&nbsp;</li>
        <li><?php echo $detail->volume_dplc ?>&nbsp;</li>
    </ul>
    <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>">Modifier</a>
    <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_delete')->getRawValue(), array('detail_key' => $key))) ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
</div>
