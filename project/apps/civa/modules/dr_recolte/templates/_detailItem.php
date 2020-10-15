<?php use_helper('Float') ?>
<div class="col_recolte col_validee">
    <h2><?php echo $produit->getConfig()->libelle ?></h2>
    <div class="col_cont">
        <?php if ($produit->getAppellation()->getConfig()->hasLieuEditable()): ?>
            <p class="lieu">
                <input data-form-input-id="detail_lieu" type="text" readonly="readonly" class="readonly" value="<?php echo $detail->lieu; ?>" title="<?php echo $detail->lieu; ?>" />
            </p>
        <?php endif; ?>

        <p class="denomination">
            <?php if ($produit->getConfig()->hasDenomination()) : ?>
                <input data-form-input-id="detail_denomination" type="text" readonly="readonly" class="readonly" value="<?php echo $detail->denomination ?>" title="<?php echo $detail->denomination ?>" />
            <?php endif; ?>
        </p>

        <p class="mention">
            <?php if ($produit->getConfig()->hasVtsgn()) : ?>
                <select disabled="disabled">
                    <option <?php if (!$detail->vtsgn): ?>selected="selected"<?php endif; ?>><option>
                    <option <?php if ($detail->vtsgn == 'VT'): ?>selected="selected"<?php endif; ?>>VT</option>
                    <option <?php if ($detail->vtsgn == 'SGN'): ?>selected="selected"<?php endif; ?>>SGN</option>
                </select>
            <?php endif; ?>
        </p>

        <p class="superficie">
            <?php if ($produit->getConfig()->hasSuperficie()) : ?>
                <input data-form-input-id="detail_superficie" type="text" class="num superficie readonly" value="<?php echoFloat($detail->superficie); ?>" />
            <?php endif; ?>
        </p>

        <?php if (!$produit->getConfig()->hasNoNegociant()): ?>
            <div class="vente_raisins">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                    'acheteurs_value' => $detail->getVolumeAcheteurs('negoces')));
                ?>
            </div>
        <?php endif; ?>

        <?php if (!$produit->getConfig()->hasNoCooperative()): ?>
            <div class="caves">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                    'acheteurs_value' => $detail->getVolumeAcheteurs('cooperatives')))
                ?>
            </div>
        <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$produit->getConfig()->hasNoMout()): ?>
            <div class="mouts">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                    'acheteurs_value' => $detail->getVolumeAcheteurs('mouts')))
                ?>
                <!--<a href="#" class="ajout_mout">Ajouter un acheteur de mouts</a>-->
            </div>
        <?php endif; ?>

        <p class="vol_place"><input data-form-input-id="detail_cave_particuliere" type="text" class="num cave readonly" value="<?php echoFloat($detail->cave_particuliere); ?>" /></p>
        <p class="vol_total_recolte">
            <input data-form-input-id="detail_vol_total_recolte" type="text" class="num total readonly" readonly="readonly" value="<?php echoFloat($detail->volume); ?>" />
            <?php if (!$produit->getConfig()->hasNoMotifNonRecolte() && $detail->hasMotifNonRecolteLibelle()) : ?>
                <a href="<?php echo url_for("dr_recolte_motif_non_recolte", array("id" => $produit->getDocument()->_id, "hash" => $produit->getHash(), 'detail_key' => $detail->getKey())) ?>" class="ajout ajout_motif <?php if ($detail->getMotifNonRecolteLibelle() != 'Déclaration en cours')
                echo 'ajout_lien'; ?>"><?php echo str_replace(" ", "&nbsp;", $detail->getMotifNonRecolteLibelle()); ?></a>
        <?php endif; ?>
        </p>
            <ul class="vol_revendique_dplc">
                <?php if ($detail->getConfig()->existRendement()): ?>
                <li>
                    <input data-form-input-id="detail_volume_revendique" type="<?php echo (!$detail->canHaveUsagesLiesSaisi()) ? 'hidden' : 'text' ?>" class="num revendique readonly" readonly="readonly" value="<?php echoFloat($detail->volume_revendique); ?>" />
                </li>
                <li>
                    <input type="hidden" class="num usages_industriels readonly" readonly="readonly" value="<?php echoFloat($detail->usages_industriels); ?>" />
                    <input data-form-input-id="detail_lies" type="<?php echo (!$detail->canHaveUsagesLiesSaisi()) ? 'hidden' : 'text' ?>" class="num lies readonly" readonly="readonly" value="<?php echoFloat($detail->lies); ?>" />
                </li>
                <?php endif; ?>
                <?php if($produit->canHaveVci()): ?>
                <li>
                    <input data-form-input-id="detail_vci" type="<?php echo (!$detail->canHaveVci()) ? 'hidden' : 'text' ?>" class="num vci readonly" readonly="readonly" value="<?php echoFloat($detail->vci); ?>" />
                </li>
                <?php endif; ?>
            </ul>
            <ul>
                <li></li>
                <li></li>
            </ul>
    </div>

    <div class="col_btn">
        <a href="<?php echo url_for('dr_recolte_produit_edition', array('id' => $detail->getDocument()->_id, 'hash' => $produit->getHash(), 'detail_key' => $detail->getKey())) ?>" class="modifier_tmp btn_recolte_can_be_inactif"><img src="/images/boutons/btn_modifier_col_cepage.png" alt="Modifier" /></a>
        <a href="<?php echo url_for('dr_recolte_produit_suppression', array('id' => $detail->getDocument()->_id, 'hash' => $produit->getHash(), 'detail_key' => $detail->getKey())) ?>" class="supprimer_tmp btn_recolte_can_be_inactif" onclick="return confirm('Etes vous sûr(e) de vouloir supprimer de détail ?')">
            <img src="/images/boutons/btn_supprimer_col_cepage.png" alt="Supprimer" />
        </a>
    </div>
</div>
