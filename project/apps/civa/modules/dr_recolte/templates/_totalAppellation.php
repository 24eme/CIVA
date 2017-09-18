<?php use_helper('Float') ?>
<div id="col_recolte_totale" class="col_recolte col_total">
    <h2>Total
        <?php if($produit->getAppellation()->getConfig()->hasManyLieu()): ?>
            <?php echo $lieu->getConfig()->libelle ?> <?php echo $produit->getMention()->getConfig()->libelle ?>
        <?php else: ?>
            <?php echo $produit->getAppellation()->getConfig()->libelle ?> <?php echo $produit->getMention()->getConfig()->libelle ?>
        <?php endif; ?>
    </h2>
    <div class="col_cont">
        <?php if ($produit->getAppellation()->getConfig()->hasLieuEditable()): ?>
        <p class="lieu">&nbsp;</p>
        <?php endif; ?>
        <p class="denomination">&nbsp;</p>
        <p class="mention">&nbsp;</p>
        <p class="superficie">
               <input id="appellation_total_superficie_orig" type="hidden" value="<?php echoFloat($lieu->getTotalSuperficie()); ?>" />
            <input id="appellation_total_superficie" type="text" class="num" readonly="readonly" value="<?php echoFloat($lieu->getTotalSuperficie()); ?>" />
        </p>

        <?php if (!$produit->getConfig()->hasNoNegociant()): ?>
        <div class="vente_raisins">
                <?php include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                                                              'acheteurs_value' => $lieu->getVolumeAcheteurs('negoces')));?>
        </div>
        <?php endif; ?>

        <?php if (!$produit->getConfig()->hasNoCooperative()): ?>
        <div class="caves">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                                                              'acheteurs_value' => $lieu->getVolumeAcheteurs('cooperatives')))
                ?>
        </div>
        <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$produit->getConfig()->hasNoMout()): ?>
        <div class="mouts">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                                                              'acheteurs_value' => $lieu->getVolumeAcheteurs('mouts')))
                ?>
        </div>
        <?php endif; ?>

        <p class="vol_place">
            <input type="hidden" id="appellation_total_cave_orig" value="<?php echoFloat($lieu->getTotalCaveParticuliere()); ?>" />
            <input type="text" id="appellation_total_cave" class="num" readonly="readonly" value="<?php echoFloat( $lieu->getTotalCaveParticuliere()); ?>" />
        </p>
        <p class="vol_total_recolte">
            <input id="appellation_total_volume_orig" type="hidden" value="<?php echoFloat( $lieu->getTotalVolume()); ?>" />
            <input id="appellation_total_volume" type="text" class="num" readonly="readonly" value="<?php echoFloat( $lieu->getTotalVolume()); ?>" />
        </p>
        <ul class="vol_revendique_dplc">
            <?php if ($lieu->getConfig()->existRendement()): ?>
            <li class="rendement <?php if ($lieu->getConfig()->hasRendementNoeud() && round($lieu->getRendementRecoltant()) > round($lieu->getRendementMax())): echo 'rouge'; endif;?>">Rdt : <strong><span id="appellation_current_rendement"><?php echo round($lieu->getRendementRecoltant(),0); ?></span>&nbsp;hl/ha</strong><span class="picto_rdt_aide_col_total"><a href="" class="msg_aide" rel="help_popup_DR_total_appellation" title="Message aide"></a></span></li>
            <?php endif; ?>
            <?php if ($lieu->getConfig()->existRendement()) : ?>
            <li>
                    <input class="num <?php if ($lieu->getDplc() > 0) echo 'rouge'; ?>" type="text" id="appellation_volume_revendique" readonly="readonly" value="<?php echoFloat($lieu->getVolumeRevendique()); ?>" />
                    <input type="hidden" id="appellation_volume_revendique_orig" readonly="readonly" value="<?php echoFloat($lieu->getVolumeRevendique()); ?>" />
            </li>
            <li>
                <input type="hidden" id="appellation_volume_dplc" readonly="readonly" value="<?php echoFloat($lieu->getDplc()); ?>" />
                <input type="hidden" id="appellation_volume_dplc_orig" readonly="readonly" class="alerte" value="<?php echoFloat( $lieu->getDplc()); ?>"/>

                <input type="text" class="num <?php if ($lieu->getDplc() > 0) echo 'rouge'; ?>" id="appellation_usages_industriels" readonly="readonly" value="<?php echoFloat($lieu->getUsagesIndustriels()); ?>"/>
                <input type="hidden" id="appellation_usages_industriels_orig" readonly="readonly" value="<?php echoFloat($lieu->getUsagesIndustriels()); ?>"/>

                <input type="hidden" id="appellation_lies" readonly="readonly" <?php if($lieu->isLiesSaisisCepage()) echo "mode='sum'" ?> class="num" value="<?php echoFloat($lieu->getLies()); ?>" />
                <input type="hidden" id="appellation_lies_orig" readonly="readonly" value="<?php echoFloat($lieu->getLies()); ?>" />
            </li>
            <?php endif; ?>
            <?php if($lieu->canHaveVci()): ?>
            <li>
                <input class="num <?php if ($lieu->getTotalVci() > $lieu->getVolumeVciMax()) echo 'rouge'; ?>" type="text" id="appellation_vci" readonly="readonly" value="<?php echoFloat($lieu->getTotalVci()); ?>" />
                <input type="hidden" id="appellation_vci_orig" readonly="readonly" value="<?php echoFloat($lieu->getTotalVci()); ?>" />
                <input type="hidden" id="appellation_rendement_vci" readonly="readonly" value="<?php echoFloat($lieu->getConfigRendementVci()); ?>" />
            </li>
            <?php endif; ?>
      </ul>
      <ul>
            <?php if ($lieu->getConfig()->hasRendementNoeud()):?>
            <li>
	            <input type="hidden" id="appellation_max_volume" value="<?php echoFloat($lieu->getVolumeMaxRendement()); ?>"/>
                <input type="hidden" id="appellation_max_rendement" value="<?php echoFloat($lieu->getRendementMax()); ?>"/>
                <input type="hidden" id="appellation_rendement" value="<?php echoFloat($lieu->getConfig()->getRendementNoeud()); ?>"/>
                <input type="text" id="appellation_dplc_rendement" class="num <?php if ($lieu->getDplcRendement() > 0) echo 'rouge'; ?> <?php if ($lieu->getDplcRendement() > 0 && $lieu->getDplc() == $lieu->getDplcRendement()) echo 'alerte'; ?>" value="<?php echoFloat($lieu->getDplcRendement()); ?>"/>
                <input type="hidden" id="appellation_dplc_rendement_orig" readonly="readonly" value="<?php echoFloat($lieu->getDplcRendement()); ?>"/>
            </li>
            <?php endif; ?>
            <?php if ($lieu->getConfig()->existRendementCepage()):?>
            <li>
                <input type="text" id="appellation_total_dplc_sum" class="<?php if ($lieu->getDplcTotal() > 0) echo 'rouge'; ?> <?php if ($lieu->getDplcTotal() > 0 && $lieu->getDplc() == $lieu->getDplcTotal()) echo 'alerte'; ?>" readonly="readonly" value="Σ <?php echoFloat($lieu->getDplcTotal()); ?>"/>
                <input type="hidden" id="appellation_total_dplc_sum_orig" class="num" value="<?php echoFloat($lieu->getDplcTotal()); ?>"/>
            </li>
            <?php endif; ?>
      </ul>
    </div>
</div>
