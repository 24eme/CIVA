<div class="col_recolte col_validee">
    <h2><?php echo $onglets->getCurrentCepage()->getConfig()->libelle ?></h2>

    <div class="col_cont">
        <p class="denomination">
            <input type="text" disabled="disabled" value="<?php echo $detail->denomination ?>" />
        </p>

        <p class="mention">
            <select disabled="disabled">
                <option <?php if(!$detail->vtsgn): ?>selected="selected"<?php endif; ?>><option>
                <option <?php if($detail->vtsgn == 'VT'): ?>selected="selected"<?php endif; ?>>VT</option>
                <option <?php if($detail->vtsgn == 'SGN'): ?>selected="selected"<?php endif; ?>>SGN</option>
            </select>
        </p>

        <p class="superficie">
            <input type="text" class="num" disabled="disabled" value="<?php echo $detail->superficie ?>" />
        </p>

        <div class="vente_raisins">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                                                             'acheteurs_value' => $detail->getAcheteursValuesWithCvi('negoces')))
                ?>
        </div>

        <div class="caves">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                                                             'acheteurs_value' => $detail->getAcheteursValuesWithCvi('cooperatives'))) ?>
        </div>
        <?php if ($has_acheteurs_mout): ?>
        <div class="mouts">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                                                             'acheteurs_value' => $detail->getAcheteursValuesWithCvi('mouts'))) ?>
            <!--<a href="#" class="ajout_mout">Ajouter un acheteur de mouts</a>-->
        </div>
        <?php endif; ?>

        <p class="vol_place"><input type="text" class="num" disabled="disabled" value="<?php echo $detail->cave_particuliere ?>" /></p>
        <p class="vol_total_recolte"><input type="text" class="num" readonly="readonly" value="<?php echo $detail->volume ?>" /></p>

        <ul class="vol_revendique_dplc">
            <li><input type="text" class="num" value="<?php echo $detail->volume_revendique ?>" /></li>
            <li><input type="text" class="num" readonly="readonly" value="<?php echo $detail->volume_dplc ?>" /></li>
        </ul>
    </div>

    <div class="col_btn">
        <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>" class="modifier_tmp"><img src="/images/boutons/btn_modifier_col_cepage.png" alt="Modifier" /></a>
        <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_delete')->getRawValue(), array('detail_key' => $key))) ?>" class="supprimer_tmp" onclick="return confirm('Etes vous sûr(e) de vouloir supprimer de détail ?')"><img src="/images/boutons/btn_supprimer_col_cepage.png" alt="Supprimer" /></a>
    </div>
</div>
