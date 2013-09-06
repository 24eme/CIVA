<?php use_helper('Float') ?>
<div class="col_recolte col_validee">
    <h2><?php echo $onglets->getCurrentCepage()->getConfig()->libelle ?></h2>

    <div class="col_cont">

        <?php if ($onglets->getCurrentAppellation()->getConfig()->hasLieuEditable()): ?>
            <p class="lieu">
                <input type="text" readonly="readonly" class="readonly" value="<?php echo $detail->lieu; ?>" title="<?php echo $detail->lieu; ?>" />
            </p>
        <?php endif; ?>

        <p class="denomination">
            <?php if ($onglets->getCurrentCepage()->getConfig()->hasDenomination()) : ?>
                <input type="text" readonly="readonly" class="readonly" value="<?php echo $detail->denomination ?>" title="<?php echo $detail->denomination ?>" />
            <?php endif; ?>
        </p>

        <p class="mention">
            <?php if ($onglets->getCurrentCepage()->getConfig()->hasVtsgn()) : ?>
                <select disabled="disabled">
                    <option <?php if (!$detail->vtsgn): ?>selected="selected"<?php endif; ?>><option>
                    <option <?php if ($detail->vtsgn == 'VT'): ?>selected="selected"<?php endif; ?>>VT</option>
                    <option <?php if ($detail->vtsgn == 'SGN'): ?>selected="selected"<?php endif; ?>>SGN</option>
                </select>
            <?php endif; ?>
        </p>

        <p class="superficie">
            <?php if ($onglets->getCurrentCepage()->getConfig()->hasSuperficie()) : ?>
                <input type="text" class="num superficie readonly" disabled="disabled" value="<?php echoFloat($detail->superficie); ?>" />
            <?php endif; ?>
        </p>

        <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoNegociant()): ?>
            <div class="vente_raisins">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                    'acheteurs_value' => $detail->getVolumeAcheteurs('negoces')));
                ?>
            </div>
        <?php endif; ?>

        <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoCooperative()): ?>
            <div class="caves">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                    'acheteurs_value' => $detail->getVolumeAcheteurs('cooperatives')))
                ?>
            </div>
        <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$onglets->getCurrentCepage()->getConfig()->hasNoMout()): ?>
            <div class="mouts">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                    'acheteurs_value' => $detail->getVolumeAcheteurs('mouts')))
                ?>
                <!--<a href="#" class="ajout_mout">Ajouter un acheteur de mouts</a>-->
            </div>
        <?php endif; ?>

        <p class="vol_place"><input type="text" class="num cave readonly" disabled="disabled" value="<?php echoFloat($detail->cave_particuliere); ?>" /></p>
        <p class="vol_total_recolte">
            <input type="text" class="num total readonly" readonly="readonly" value="<?php echoFloat($detail->volume); ?>" />
            <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoMotifNonRecolte() && $detail->hasMotifNonRecolteLibelle()) : ?>
                <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_motif_non_recolte', null, null, null, null, null, null)->getRawValue(), array('detail_key' => $detail->getKey()))) ?>" class="ajout ajout_motif <?php if ($detail->getMotifNonRecolteLibelle() != 'Déclaration en cours')
                echo 'ajout_lien'; ?>"><?php echo $detail->getMotifNonRecolteLibelle(); ?></a>
        <?php endif; ?>
        </p>
        <?php if ($detail->getConfig()->existRendement()): ?>
            <ul class="vol_revendique_dplc">
                <li>
                    <input type="hidden" class="num revendique readonly" readonly="readonly" value="<?php echoFloat($detail->volume_revendique); ?>" />
                    </li>
                <li>
                    <input type="hidden" class="num usages_industriels readonly" readonly="readonly" value="<?php echoFloat($detail->usages_industriels); ?>" />
                    <input type="<?php echo (!$detail->canHaveUsagesLiesSaisi()) ? 'hidden' : 'text' ?>" class="num lies readonly" readonly="readonly" value="<?php echoFloat($detail->lies); ?>" />
                </li>
            </ul>
            <ul>
                <li></li>
                <li><input type="hidden" class="num dplc readonly" readonly="readonly" value="<?php echoFloat($detail->volume_dplc); ?>" /></li>
            </ul>
        <?php endif; ?>
    </div>

    <div class="col_btn">
        <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_update')->getRawValue(), array('detail_key' => $key))) ?>" class="modifier_tmp btn_recolte_can_be_inactif"><img src="/images/boutons/btn_modifier_col_cepage.png" alt="Modifier" /></a>
        <a href="<?php echo url_for(array_merge($onglets->getUrl('recolte_delete')->getRawValue(), array('detail_key' => $key))) ?>" class="supprimer_tmp btn_recolte_can_be_inactif" onclick="return confirm('Etes vous sûr(e) de vouloir supprimer de détail ?')">
            <img src="/images/boutons/btn_supprimer_col_cepage.png" alt="Supprimer" />
        </a>
    </div>
</div>
