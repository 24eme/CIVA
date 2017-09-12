<?php use_helper('Float') ?>
<div id="col_couleur_totale" class="col_recolte col_total col_calcule <?php echo (count($produit->getLieu()->getConfig()->getCouleurs()) > 1) ? "col_double" : "" ?>">
    <h2>Total
        <?php echo $produit->getAppellation()->getConfig()->libelle ?>
        <strong><?php echo $couleur->getLibelle() ?></strong>
        <a href="" class="msg_aide" rel="help_popup_DR_total_couleur" title="Message aide"></a>
    </h2>



    <div class="col_cont">
        <?php if ($produit->getAppellation()->getConfig()->hasLieuEditable()): ?>
            <p class="lieu">&nbsp;</p>
        <?php endif; ?>
        <p class="denomination">&nbsp;</p>
        <p class="mention">&nbsp;</p>
        <p class="superficie">
            <input id="appellation_total_superficie_orig" type="hidden" value="<?php echoFloat($couleur->getTotalSuperficie()); ?>" />
            <input id="appellation_total_superficie" type="text" class="num" readonly="readonly" value="<?php echoFloat($couleur->getTotalSuperficie()); ?>" />
        </p>

        <?php if (!$produit->getConfig()->hasNoNegociant()): ?>
            <div class="vente_raisins">
                <?php include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                    'acheteurs_value' => $couleur->getVolumeAcheteurs('negoces'))); ?>
            </div>
        <?php endif; ?>

        <?php if (!$produit->getConfig()->hasNoCooperative()): ?>
            <div class="caves">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                    'acheteurs_value' => $couleur->getVolumeAcheteurs('cooperatives')))
                ?>
            </div>
        <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$produit->getConfig()->hasNoMout()): ?>
            <div class="mouts">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                    'acheteurs_value' => $couleur->getVolumeAcheteurs('mouts')))
                ?>
            </div>
        <?php endif; ?>

        <p class="vol_place">
            <input type="hidden" id="appellation_total_cave_orig" value="<?php echoFloat($couleur->getTotalCaveParticuliere()); ?>" />
            <input type="text" class="num" id="appellation_total_cave" readonly="readonly" value="<?php echoFloat($couleur->getTotalCaveParticuliere()); ?>" />
        </p>
        <p class="vol_total_recolte">
            <input id="appellation_total_volume_orig" type="hidden" value="<?php echoFloat($couleur->getTotalVolume()); ?>" />
            <input id="appellation_total_volume" class="num" type="text" readonly="readonly" value="<?php echoFloat($couleur->getTotalVolume()); ?>" />
        </p>
        <ul class="vol_revendique_dplc">
            <?php if ($couleur->getConfig()->hasRendementCouleur()): ?>
            <li class="rendement <?php if (round($couleur->getRendementRecoltant()) > round($couleur->getConfig()->getRendementNoeud())): echo 'rouge'; endif;?>">Rdt : <strong><span id="appellation_current_rendement"><?php echo round($couleur->getRendementRecoltant(), 0); ?></span>&nbsp;hl/ha</strong><span class="picto_rdt_aide_col_total"><a href="" class="msg_aide" rel="help_popup_DR_total_appellation" title="Message aide"></a></span></li>
            <?php endif; ?>
            <?php if ($couleur->getConfig()->hasRendementCouleur()): ?>
            <li>
                <input type="text" id="appellation_volume_revendique" class="num <?php if ($couleur->getDplc() > 0) echo 'rouge'; ?>" readonly="readonly" value="<?php echoFloat($couleur->getVolumeRevendique()); ?>" />
                <input type="hidden" id="appellation_volume_revendique_orig" readonly="readonly" value="<?php echoFloat($couleur->getVolumeRevendique()); ?>" />
            </li>
            <li>
                <input type="hidden" id="appellation_volume_dplc" readonly="readonly" value="<?php echoFloat($couleur->getDplc()); ?>"/>
                <input type="hidden" id="appellation_volume_dplc_orig" readonly="readonly" value="<?php echoFloat($couleur->getDplc()); ?>"/>

                <input type="text" id="appellation_usages_industriels"  class="num <?php if ($couleur->getDplc() > 0) echo 'rouge'; ?>" <?php if($couleur->isLiesSaisisCepage()) echo "mode='sum'" ?> readonly="readonly" value="<?php echoFloat($couleur->getUsagesIndustriels()); ?>" />
                <input type="hidden" id="appellation_usages_industriels_orig" value="<?php echoFloat($couleur->getUsagesIndustriels()); ?>" />

                <input type="hidden" id="appellation_lies" readonly="readonly" <?php if($couleur->isLiesSaisisCepage()) echo "mode='sum'" ?> class="num" value="<?php echoFloat($couleur->getLies()); ?>" />
                <input type="hidden" id="appellation_lies_orig" value="<?php echoFloat($couleur->getLies()); ?>" />
            </li>
            <?php endif; ?>
            <?php if($couleur->canHaveVci()): ?>
            <li>
                <input class="num" type="text" id="appellation_vci" readonly="readonly" value="<?php echoFloat($couleur->getTotalVci()); ?>" />
                <input type="hidden" id="appellation_vci_orig" readonly="readonly" value="<?php echoFloat($couleur->getTotalVci()); ?>" />
            </li>
            <?php endif; ?>
        </ul>
        <ul>
            <li>
            <?php if ($couleur->getConfig()->hasRendementNoeud()):?>
                <input type="hidden" id="appellation_rendement" value="<?php echoFloat($couleur->getConfig()->getRendementCouleur()); ?>"/>
                <input type="hidden" id="appellation_max_volume" value="<?php echoFloat($couleur->getVolumeMaxRendement()); ?>"/>
                <input type="text" id="appellation_dplc_rendement" class="num <?php if ($couleur->getDplcRendement() > 0) echo 'rouge'; ?> <?php if ($couleur->getDplcRendement() > 0 && $couleur->getDplc() == $couleur->getDplcRendement()) echo 'alerte'; ?>" readonly="readonly" value="<?php echoFloat($couleur->getDplcRendement()); ?>"/>
                <input type="hidden" id="appellation_dplc_rendement_orig" value="<?php echoFloat($couleur->getDplcRendement()); ?>"/>
            <?php endif; ?>
            </li>
            <li>
            <?php if ($couleur->getConfig()->existRendementCepage()):?>
                <input type="text" id="appellation_total_dplc_sum_orig" class="num <?php if ($couleur->getDplcTotal() > 0) echo 'rouge'; ?> <?php if ($couleur->getDplcTotal() > 0 && $couleur->getDplc() == $couleur->getDplcTotal()) echo 'alerte'; ?>" value="<?php echoFloat($couleur->getDplcTotal()); ?>"/>
                <input type="hidden" id="appellation_total_dplc_sum" readonly="readonly" value="Σ <?php echoFloat($couleur->getDplcTotal()); ?>"/>
            <?php endif; ?>
            </li>
        </ul>
    </div>
</div>
