<?php use_helper('Float') ?>
<div id="col_cepage_total" class="col_recolte col_total" style="<?php if (!$cepage->getConfig()->hasTotalCepage() /*|| $produit->getLieu()->getConfig()->hasManyCouleur()*/): ?>display:none;<?php endif; ?>">
    <h2>Total <br /> <?php echo $cepage->libelle ?> <?php echo $cepage->getMention()->libelle ?></h2>

    <div class="col_cont">
        <?php if ($produit->getAppellation()->getConfig()->hasLieuEditable()): ?>
            <p class="lieu">&nbsp;</p>
        <?php endif; ?>
        <p class="denomination">&nbsp;</p>
        <p class="mention">&nbsp;</p>
        <p class="superficie">
            <input type="hidden" id="cepage_total_superficie_orig" value="<?php echoFloat($cepage->getTotalSuperficie()); ?>" />
            <input type="text" id="cepage_total_superficie" class="num" readonly="readonly" value="<?php echoFloat($cepage->getTotalSuperficie()); ?>" />
        </p>
        <?php  if (!$produit->getConfig()->hasNoNegociant()): ?>
        <div class="vente_raisins">
                <?php
                    include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                                                              'acheteurs_value' => $cepage->getVolumeAcheteurs('negoces'))); ?>
        </div>
        <?php endif; ?>

        <?php if (!$produit->getConfig()->hasNoCooperative()): ?>
        <div class="caves">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                                                              'acheteurs_value' => $cepage->getVolumeAcheteurs('cooperatives')))
                ?>
        </div>
        <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$produit->getConfig()->hasNoMout()): ?>
        <div class="mouts">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                                                              'acheteurs_value' => $cepage->getVolumeAcheteurs('mouts')))
                ?>
        </div>
        <?php endif; ?>

        <p class="vol_place">
            <input type="hidden" id='cepage_total_cave_orig' value="<?php echoFloat($cepage->getTotalCaveParticuliere()); ?>" />
            <input type="text" id='cepage_total_cave' class="num" readonly="readonly" value="<?php echoFloat($cepage->getTotalCaveParticuliere()); ?>" />
        </p>
        <p class="vol_total_recolte">
        <?php if ($cepage->getConfig()->hasRendementNoeud()): ?>
            <input type="hidden" id='cepage_rendement' value="<?php echoFloat($cepage->getConfig()->getRendementNoeud()); ?>" />
            <input type="hidden" id='cepage_max_rendement' value="<?php echoFloat($cepage->getRendementMax()); ?>" />
            <input type="hidden" id='cepage_max_volume' value="<?php echoFloat($cepage->getVolumeMaxRendement()); ?>" />
        <?php else: ?>
            <input type="hidden" id='cepage_rendement' value="-1" />
            <input type="hidden" id='cepage_max_rendement' value="-1" />
            <input type="hidden" id='cepage_max_volume' value="<?php echoFloat($cepage->getTotalVolume()); ?>" />
        <?php endif; ?>
            <input type="hidden" id='cepage_total_volume_orig' value="<?php echoFloat($cepage->getTotalVolume()); ?>" />
            <input type="text" id='cepage_total_volume' class="num" readonly="readonly" value="<?php echoFloat($cepage->getTotalVolume()); ?>" />
        </p>
        <ul class="vol_revendique_dplc">
            <?php if ($cepage->getConfig()->hasRendementNoeud()): ?>
            <li class="rendement <?php if (round($cepage->getRendementRecoltant()) > round($cepage->getRendementMax())): echo 'rouge'; endif;?>">Rdt : <strong><span id="cepage_current_rendement"><?php echo round($cepage->getRendementRecoltant(),0); ?></span> hl/ha</strong><span  class="picto_rdt_aide_col_total"><a href="" class="msg_aide" rel="help_popup_DR_total_cepage" title="Message aide"></a></span></li>
            <?php endif; ?>
            <li>
                <input type="text" class="num <?php if ($cepage->getDplc() > 0) echo 'rouge'; ?>" id="cepage_volume_revendique" readonly="readonly" value="<?php echoFloat($cepage->getVolumeRevendique()); ?>" />
                <input type="hidden" id="cepage_volume_revendique_orig" value="<?php echoFloat($cepage->getVolumeRevendique()); ?>" />
            </li>
            <li>
                <input type="hidden" id="cepage_volume_dplc" readonly="readonly" value="<?php echoFloat($cepage->getDplc()); ?>" />
                <input type="hidden" id="cepage_volume_dplc_orig" value="<?php echoFloat($cepage->getDplc()); ?>" />
                <input type="hidden" id="cepage_lies" readonly="readonly" value="<?php echoFloat($cepage->getLies()); ?>" />
                <input type="hidden" id="cepage_lies_orig" value="<?php echoFloat($cepage->getLies()); ?>" />
                <input type="text" id="cepage_usages_industriels" readonly="readonly" class="num <?php if ($cepage->getDplc() > 0) echo 'rouge'; ?>" value="<?php echoFloat($cepage->getUsagesIndustriels()); ?>" />
                <input type="hidden" id="cepage_usages_industriels_orig" value="<?php echoFloat($cepage->getUsagesIndustriels()); ?>" />
            </li>
            <?php if($cepage->getLieu()->canHaveVci()): ?>
            <li>
                <input type="text" id="cepage_vci" readonly="readonly" class="num <?php if ($cepage->getVolumeVciMax() != -1 && $cepage->getTotalVci() > $cepage->getVolumeVciMax()) echo 'rouge'; ?>" value="<?php echoFloat($cepage->getTotalVci()); ?>" />
                <input type="hidden" id="cepage_vci_orig" value="<?php echoFloat($cepage->getTotalVci()); ?>" />
                <input type="hidden" id="cepage_rendement_vci" readonly="readonly" value="<?php echoFloat($cepage->getConfigRendementVci()); ?>" />
            </li>
            <?php endif; ?>
        </ul>
        <ul>
             <?php if ($cepage->getConfig()->existRendementAppellation() || $cepage->getConfig()->existRendementCouleur()):?>
            <li>
            </li>
            <?php endif; ?>
            <?php if ($cepage->getConfig()->hasRendementNoeud()):?>
            <li>

                <input type="text" id="cepage_dplc_rendement" class="num <?php if ($cepage->getDplcRendement() > 0) echo 'rouge'; ?> <?php if ($cepage->getDplcRendement() > 0 && $cepage->getDplc() == $cepage->getDplcRendement()) echo 'alerte'; ?>" readonly="readonly" value="<?php echoFloat($cepage->getDplcRendement()); ?>" />
                <input type="hidden" id="cepage_dplc_rendement_orig" value="<?php echoFloat($cepage->getDplcRendement()); ?>" />
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
