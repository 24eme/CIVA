<div class="col_recolte col_validee">
    <h2><?php echo $onglets->getCurrentCepage()->getConfig()->libelle ?></h2>

    <div class="col_cont">
        <p class="denomination">
<?php if ($onglets->getCurrentCepage()->getConfig()->hasDenomination()) : ?>
            <input type="text" disabled="disabled" value="<?php echo $detail->denomination ?>" />
<?php endif;?>
        </p>

        <p class="mention">
<?php if ($onglets->getCurrentCepage()->getConfig()->hasVtsgn()) : ?>
            <select disabled="disabled">
                <option <?php if(!$detail->vtsgn): ?>selected="selected"<?php endif; ?>><option>
                <option <?php if($detail->vtsgn == 'VT'): ?>selected="selected"<?php endif; ?>>VT</option>
                <option <?php if($detail->vtsgn == 'SGN'): ?>selected="selected"<?php endif; ?>>SGN</option>
            </select>
<?php endif; ?>
        </p>

        <p class="superficie">
<?php if ($onglets->getCurrentCepage()->getConfig()->hasSuperficie()) : ?>
            <input type="text" class="num superficie" disabled="disabled" value="<?php echo $detail->superficie ?>" />
<?php endif; ?>
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

        <p class="vol_place"><input type="text" class="num cave" disabled="disabled" value="<?php echo $detail->cave_particuliere ?>" /></p>
        <p class="vol_total_recolte">
            <input type="text" class="num total readonly" readonly="readonly" value="<?php echo $detail->volume ?>" />
            <?php if ($detail->hasMotifNonRecolteLibelle()){ ?>
                <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_motif_non_recolte')->getRawValue(), array('detail_key' => $detail->getKey()))) ?>" class="ajout ajout_motif <?php if($detail->getMotifNonRecolteLibelle() != 'Non saisie') echo 'ajout_lien'; ?>"><?php echo $detail->getMotifNonRecolteLibelle(); ?></a>
            <?php } ?>
        </p>
        
        <?php if ($detail->hasRendementCepage()): ?>
        <ul class="vol_revendique_dplc">
            <li><input type="hidden" class="num revendique readonly" value="<?php echo $detail->volume_revendique ?>" /></li>
            <li><input type="hidden" class="num dplc readonly" readonly="readonly" value="<?php echo $detail->volume_dplc ?>" /></li>
        </ul>
        <?php endif; ?>
    </div>

    <div class="col_btn">
        <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>" class="modifier_tmp <?php if($is_detail_edit): ?>btn_inactif<?php endif; ?>"><img src="/images/boutons/btn_modifier_col_cepage.png" alt="Modifier" /></a>
        <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_delete')->getRawValue(), array('detail_key' => $key))) ?>" class="supprimer_tmp <?php if($is_detail_edit): ?>btn_inactif<?php endif; ?>" <?php if(!$is_detail_edit): ?> onclick="return confirm('Etes vous sûr(e) de vouloir supprimer de détail ?')" <?php endif; ?>>
            <img src="/images/boutons/btn_supprimer_col_cepage.png" alt="Supprimer" />
        </a>
    </div>
</div>
