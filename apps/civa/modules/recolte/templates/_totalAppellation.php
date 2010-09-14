<?php use_helper('civa') ?>
<div id="col_recolte_totale" class="col_recolte col_total">
    <h2>Total 
    <?php if($onglets->getCurrentAppellation()->hasManyLieu()): ?>
        <?php echo $lieu->getConfig()->libelle ?>
    <?php else: ?>
        <?php echo $onglets->getCurrentAppellation()->getConfig()->libelle ?>
    <?php endif; ?>
    </h2>



    <div class="col_cont">
        <p class="denomination">&nbsp;</p>
        <p class="mention">&nbsp;</p>
        <p class="superficie">
               <input id="appellation_total_superficie_orig" type="hidden" value="<?php echoFloat($lieu->getTotalSuperficie()); ?>" />
            <input id="appellation_total_superficie" type="text" readonly="readonly" value="<?php echoFloat($lieu->getTotalSuperficie()); ?>" />
        </p>

        <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoNegociant()): ?>
        <div class="vente_raisins">
                <?php include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                                                              'acheteurs_value' => $lieu->getTotalAcheteursByCvi('negoces')));?>
        </div>
        <?php endif; ?>

        <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoCooperative()): ?>
        <div class="caves">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                                                              'acheteurs_value' => $lieu->getTotalAcheteursByCvi('cooperatives')))
                ?>
        </div>
        <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$onglets->getCurrentCepage()->getConfig()->hasNoMout()): ?>
        <div class="mouts">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                                                              'acheteurs_value' => $lieu->getTotalAcheteursByCvi('mouts')))
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
    <li class="rendement <?php if ($lieu->getTotalDPLC()) echo 'alerte'; ?>">Rdt : <strong><span id="appellation_current_rendement"><?php echoFloat( $lieu->getRendementRecoltant()); ?></span>&nbsp;hl/ha</strong><span><a href="" class="msg_aide" rel="help_popup_DR_total_appellation" title="Message aide">Test message d'aide</a></span></li>
            <?php if ($lieu->hasRendement()): ?>
                <?php if ($lieu->hasRendementAppellation()): ?>
		    <input type="hidden" id="appellation_max_volume" value="<?php echoFloat( $lieu->getVolumeMaxAppellation()); ?>"/>
		       <input type="hidden" id="appellation_rendement" value="<?php echoFloat( $lieu->getRendementAppellation()); ?>"/>

                    <li>
		       <input type="hidden" id="appellation_volume_revendique_orig" readonly="readonly" value="<?php echoFloat( $lieu->getVolumeRevendiqueAppellation()); ?>" />
		       <input type="text" id="appellation_volume_revendique" readonly="readonly" value="<?php echoFloat( $lieu->getVolumeRevendiqueAppellation()); ?>" />
		       </li>
                    <li><input type="hidden" id="appellation_volume_dplc_orig" readonly="readonly" class="alerte" value="<?php echoFloat( $lieu->getDPLCAppellation()); ?>"/>
                    <input type="text" id="appellation_volume_dplc" readonly="readonly" class="<?php if ($lieu->getDPLCAppellation()) echo 'alerte'; ?>" value="<?php echoFloat( $lieu->getDPLCAppellation()); ?>"/></li>
                <?php endif; ?>
    <?php if ($lieu->hasRendementCepage()) : ?>
                <li>
		<input type="hidden" id="appellation_total_revendique_sum_orig" readonly="readonly" value="<?php echoFloat($lieu->getTotalVolumeRevendique()); ?>" />
		<input type="text" id="appellation_total_revendique_sum" readonly="readonly" value="Σ <?php echoFloat( $lieu->getTotalVolumeRevendique()); ?>" />
   </li>
                <li>
   <input type="hidden" id="appellation_total_dplc_sum_orig" value="<?php echoFloat( $lieu->getTotalDPLC()); ?>"/>
   <input type="text" id="appellation_total_dplc_sum" readonly="readonly" class="<?php if ($lieu->getTotalDPLC()) echo 'alerte'; ?>" value="Σ <?php echoFloat( $lieu->getTotalDPLC()); ?>"/>
   </li>
            <?php endif; ?>
            <?php endif; ?>

        </ul>
    </div>
</div>