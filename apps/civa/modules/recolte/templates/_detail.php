<div id="colonne_intitules" style="margin-left: 2px;">
    <ul class="denomination_mention">
        <li><?php echo $detail->denomination ?>&nbsp;</li>
        <li><?php echo $detail->vtsgn ?>&nbsp;</li>

    </ul>

    <p class="superficie"><?php echo $detail->superficie ?>&nbsp;</p>

    <div class="vente_raisins">
        <h3>Ventes de Raisins</h3>
        <ul>
            <li>&nbsp;</li>
            <li>&nbsp;</li>

            <li>&nbsp;</li>
            <li>&nbsp;</li>
        </ul>
    </div>

    <div class="caves">
        <h3>Caves Coopératives</h3>
        <ul>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
            <li>&nbsp;</li>
        </ul>
    </div>

    <p class="vol_place"><?php echo $detail->volume ?>&nbsp;</p>

    <p class="vol_total_recolte">&nbsp;</p>

    <ul class="vol_revendique_dplc">
        <li><?php echo $detail->volume_revendique ?>&nbsp;</li>
        <li>&nbsp;</li>
    </ul>
    <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>">Modifier</a>
    <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_delete')->getRawValue(), array('detail_key' => $key))) ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
</div>
