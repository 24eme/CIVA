<?php use_helper('civa') ?>
<div id="col_recolte_totale" class="col_recolte col_total">
    <h2>Total
        <?php if($onglets->getCurrentAppellation()->getConfig()->hasManyLieu()): ?>
            <?php echo $lieu->getConfig()->libelle ?>
            <?php else: ?>
            <?php echo $onglets->getCurrentAppellation()->getConfig()->libelle ?>
            <?php endif; ?>
    </h2>




    <div class="col_cont">
        <?php if ($onglets->getCurrentAppellation()->getConfig()->hasLieuEditable()): ?>
            <p class="lieu">&nbsp;</p>
        <?php endif; ?>
        <p class="denomination">&nbsp;</p>
        <p class="mention">&nbsp;</p>
        <p class="superficie">
               <input id="appellation_total_superficie_orig" type="hidden" value="<?php echoFloat($lieu->getTotalSuperficie()); ?>" />
            <input id="appellation_total_superficie" type="text" readonly="readonly" value="<?php echoFloat($lieu->getTotalSuperficie()); ?>" />
        </p>

        <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoNegociant()): ?>
        <div class="vente_raisins">
                <?php include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                                                              'acheteurs_value' => $lieu->getVolumeAcheteurs('negoces')));?>
        </div>
        <?php endif; ?>

        <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoCooperative()): ?>
        <div class="caves">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                                                              'acheteurs_value' => $lieu->getVolumeAcheteurs('cooperatives')))
                ?>
        </div>
        <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$onglets->getCurrentCepage()->getConfig()->hasNoMout()): ?>
        <div class="mouts">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                                                              'acheteurs_value' => $lieu->getVolumeAcheteurs('mouts')))
                ?>
        </div>
        <?php endif; ?>

        <p class="vol_place">
   <input type="hidden" id="appellation_total_cave_orig" value="<?php echoFloat($lieu->getTotalCaveParticuliere()); ?>" />
   <input type="text" id="appellation_total_cave" readonly="readonly" value="<?php echoFloat( $lieu->getTotalCaveParticuliere()); ?>" />
   </p>
        <p class="vol_total_recolte">
   <input id="appellation_total_volume_orig" type="hidden" value="<?php echoFloat( $lieu->getTotalVolume()); ?>" />
   <input id="appellation_total_volume" type="text" readonly="readonly" value="<?php echoFloat( $lieu->getTotalVolume()); ?>" />
   </p>
        <ul class="vol_revendique_dplc">
   <?php if ($lieu->getConfig()->hasRendement()): ?>
         <li class="rendement <?php if ($lieu->getDplcTotal()) echo 'alerte'; ?>">Rdt : <strong><span id="appellation_current_rendement"><?php echo round($lieu->getRendementRecoltant(),0); ?></span>&nbsp;hl/ha</strong><span class="picto_rdt_aide_col_total"><a href="" class="msg_aide" rel="help_popup_DR_total_appellation" title="Message aide"></a></span></li>
   <?php endif; ?>
   <?php if ($lieu->getConfig()->hasRendementAppellation()): ?>
		    <input type="hidden" id="appellation_max_volume" value="<?php echoFloat( $lieu->getVolumeMaxAppellation()); ?>"/>
		    <input type="hidden" id="appellation_rendement" value="<?php echoFloat( $lieu->getConfig()->getRendementAppellation()); ?>"/>

            <li>
		       <input type="hidden" id="appellation_volume_revendique_orig" readonly="readonly" value="<?php echoFloat( $lieu->getVolumeRevendiqueAppellationWithUIS()); ?>" />
		       <input type="text" id="appellation_volume_revendique" readonly="readonly" value="<?php echoFloat( $lieu->getVolumeRevendiqueAppellationWithUIS()); ?>" />
		    </li>
            <li><input type="hidden" id="appellation_volume_dplc_orig" readonly="readonly" class="alerte" value="<?php echoFloat( $lieu->getUsageIndustrielCalculeAppellation()); ?>"/>
                <input type="text" id="appellation_volume_dplc" readonly="readonly"
                       class="<?php if ($lieu->getUsageIndustrielCalculeAppellation()  &&  $lieu->dplc =! 0  ) echo 'alerte'; ?>"
                       value="<?php echoFloat($lieu->getUsageIndustrielCalculeAppellation()); ?>" />
             </li>
   <?php endif; ?>
   <?php if ($lieu->getConfig()->hasRendementCepage()) : ?>
   <?php
        if($lieu->getConfig()->hasRendementAppellation()){
            $vol_revendique = $lieu->getVolumeRevendique();
        }else{
            $vol_revendique = $lieu->getVolumeRevendiqueTotalWithUIS();
        }
        ?>
        <li>
		    <input type="hidden" id="appellation_total_revendique_sum_orig" readonly="readonly" value="<?php echoFloat($vol_revendique); ?>" />
		    <input type="text" id="appellation_total_revendique_sum" readonly="readonly" value="Σ <?php echoFloat($vol_revendique)?> "/>
        </li>
        <?php
            if($lieu->getConfig()->hasRendementAppellation()){
                $dplc = $lieu->getDplc();
            }else{
                $dplc = $lieu->getUsageIndustrielCalculeTotal();
            }
         ?>
        <li>
            <input type="hidden" id="appellation_total_dplc_sum_orig" value="<?php echoFloat($dplc); ?>"/>
            <input type="text" id="appellation_total_dplc_sum" readonly="readonly" class="<?php if ($dplc) echo 'alerte'; ?>" value="Σ <?php echoFloat($dplc); ?>"/>
        </li>
   <?php endif; ?>
        </ul>
    </div>
</div>