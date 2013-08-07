<?php use_helper('Float') ?>
<div id="col_cepage_total" class="col_recolte col_total" style="<?php if (!$cepage->getConfig()->hasTotalCepage() || $onglets->getCurrentLieu()->getConfig()->hasManyCouleur()): ?>display:none;<?php endif; ?>">
    <h2>Total <br /> <?php echo $cepage->libelle ?> </h2>

    <div class="col_cont">
        <?php if ($onglets->getCurrentAppellation()->getConfig()->hasLieuEditable()): ?>
            <p class="lieu">&nbsp;</p>
        <?php endif; ?>
        <p class="denomination">&nbsp;</p>
        <p class="mention">&nbsp;</p>
        <p class="superficie">
            <input type="hidden" id="cepage_total_superficie_orig" value="<?php echoFloat($cepage->getTotalSuperficie()); ?>" />
            <input type="text" id="cepage_total_superficie" readonly="readonly" value="<?php echoFloat($cepage->getTotalSuperficie()); ?>" />
        </p>
        <?php  if (!$onglets->getCurrentCepage()->getConfig()->hasNoNegociant()): ?>
        <div class="vente_raisins">
                <?php
                    include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                                                              'acheteurs_value' => $cepage->getVolumeAcheteurs('negoces'))); ?>
        </div>
        <?php endif; ?>

        <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoCooperative()): ?>
        <div class="caves">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                                                              'acheteurs_value' => $cepage->getVolumeAcheteurs('cooperatives')))
                ?>
        </div>
        <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$onglets->getCurrentCepage()->getConfig()->hasNoMout()): ?>
        <div class="mouts">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                                                              'acheteurs_value' => $cepage->getVolumeAcheteurs('mouts')))
                ?>
        </div>
        <?php endif; ?>

        <p class="vol_place">
   <input type="hidden" id='cepage_total_cave_orig' value="<?php echoFloat($cepage->getTotalCaveParticuliere()); ?>" />
   <input type="text" id='cepage_total_cave' readonly="readonly" value="<?php echoFloat($cepage->getTotalCaveParticuliere()); ?>" />
   </p>
        <p class="vol_total_recolte">
   <?php if ($cepage->getConfig()->hasRendement()): ?>
        <input type="hidden" id='cepage_rendement' value="<?php echoFloat($cepage->getConfig()->getRendement()); ?>" />
        <input type="hidden" id='cepage_max_volume' value="<?php echoFloat($cepage->getVolumeMax()); ?>" />
   <?php else: ?>
        <input type="hidden" id='cepage_rendement' value="-1" />
        <input type="hidden" id='cepage_max_volume' value="<?php echoFloat($cepage->getTotalVolume()); ?>" />
   <?php endif; ?>
   <input type="hidden" id='cepage_total_volume_orig' value="<?php echoFloat($cepage->getTotalVolume()); ?>" />
   <input type="text" id='cepage_total_volume' readonly="readonly" value="<?php echoFloat($cepage->getTotalVolume()); ?>" />
   </p>
   <ul class="vol_revendique_dplc">
   <?php if ($cepage->getConfig()->hasRendement()): ?>
    <li class="rendement <?php if ($cepage->getDplc() > 0): echo 'alerte'; endif;?>">Rdt : <strong><span id="cepage_current_rendement"><?php echo round($cepage->getRendementRecoltant(),0); ?></span> hl/ha</strong><span  class="picto_rdt_aide_col_total"><a href="" class="msg_aide" rel="help_popup_DR_total_cepage" title="Message aide"></a></span></li>
   <?php endif; ?>
   <?php if ($onglets->getCurrentLieu()->getConfig()->hasRendement()): ?>
	   <li>
	      <input type="text" id="cepage_volume_revendique" readonly="readonly" value="<?php echoFloat($cepage->getVolumeRevendique()); ?>" />
	      <input type="hidden" id="cepage_volume_revendique_orig" value="<?php echoFloat($cepage->getVolumeRevendique()); ?>" />
	      </li>
	      <li>
        <input type="text" id="cepage_volume_usages_industriels" readonly="readonly" class="<?php if ($cepage->getUsagesIndustriels()) echo 'alerte'; ?>" value="<?php echoFloat($cepage->getUsagesIndustriels()); ?>" />
        <input type="hidden" id="cepage_volume_usages_industriels_orig" class="<?php if ($cepage->getUsagesIndustriels()) echo 'alerte'; ?>" value="<?php echoFloat($cepage->getUsagesIndustriels()); ?>" />
	      <!--<input type="text" id="cepage_volume_dplc" readonly="readonly" class="<?php if ($cepage->getDplc()) echo 'alerte'; ?>" value="<?php echoFloat($cepage->getDplc()); ?>" />
	      <input type="hidden" id="cepage_volume_dplc_orig" class="<?php if ($cepage->getDplc()) echo 'alerte'; ?>" value="<?php echoFloat($cepage->getDplc()); ?>" />-->
	      </li>
   <?php endif; ?>
        </ul>
    </div>
</div>
