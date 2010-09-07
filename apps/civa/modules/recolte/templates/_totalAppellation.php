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
               <input id="appellation_total_superficie_orig" type="hidden" value="<?php echo $lieu->getTotalSuperficie() ?>" />
            <input id="appellation_total_superficie" type="text" readonly="readonly" value="<?php echo $lieu->getTotalSuperficie() ?>" />
        </p>

        <div class="vente_raisins">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                                                              'acheteurs_value' => $lieu->getTotalAcheteursByCvi('negoces')))
                ?>
        </div>

        <div class="caves">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                                                              'acheteurs_value' => $lieu->getTotalAcheteursByCvi('cooperatives')))
                ?>
        </div>

        <?php if ($has_acheteurs_mout): ?>
        <div class="mouts">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                                                              'acheteurs_value' => $lieu->getTotalAcheteursByCvi('mouts')))
                ?>
        </div>
        <?php endif; ?>

        <p class="vol_place">
   <input type="hidden" id="appellation_total_cave_orig" value="<?php echo $lieu->getTotalCaveParticuliere() ?>" />
   <input type="text" id="appellation_total_cave" readonly="readonly" value="<?php echo $lieu->getTotalCaveParticuliere() ?>" />
   </p>
        <p class="vol_total_recolte">
   <input id="appellation_total_volume_orig" type="hidden" value="<?php echo $lieu->getTotalVolume() ?>" />
   <input id="appellation_total_volume" type="text" readonly="readonly" value="<?php echo $lieu->getTotalVolume() ?>" />
   </p>
        <ul class="vol_revendique_dplc">
            <li class="rendement">Rdt : <strong><span id="appellation_current_rendement"><?php echo $lieu->getRendementRecoltant() ?></span> hl/ha</strong></li>
            <?php if ($lieu->hasRendement()): ?>
                <?php if ($lieu->hasRendementAppellation()): ?>
		    <input type="hidden" id="appellation_max_volume" value="<?php echo $lieu->getVolumeMaxAppellation(); ?>"/>
		       <input type="hidden" id="appellation_rendement" value="<?php echo $lieu->getRendementAppellation(); ?>"/>

                    <li>
		       <input type="hidden" id="appellation_volume_revendique_orig" readonly="readonly" value="<?php echo $lieu->getVolumeRevendiqueAppellation() ?>" />
		       <input type="text" id="appellation_volume_revendique" readonly="readonly" value="<?php echo $lieu->getVolumeRevendiqueAppellation() ?>" />
		       </li>
                    <li><input type="hidden" id="appellation_volume_dplc_orig" readonly="readonly" class="alerte" value="<?php echo $lieu->getDPLCAppellation() ?>"/>
                    <input type="text" id="appellation_volume_dplc" readonly="readonly" class="<?php if ($lieu->getDPLCAppellation()) echo 'alerte'; ?>" value="<?php echo $lieu->getDPLCAppellation() ?>"/></li>
                <?php endif; ?>
                <li>
		<input type="hidden" id="appellation_total_revendique_sum_orig" readonly="readonly" value="<?php echo $lieu->getTotalVolumeRevendique() ?>" />
		<input type="text" id="appellation_total_revendique_sum" readonly="readonly" value="Σ <?php echo $lieu->getTotalVolumeRevendique() ?>" />
   </li>
                <li>
   <input type="hidden" id="appellation_total_dplc_sum_orig" value="<?php echo $lieu->getTotalDPLC() ?>"/>
   <input type="text" id="appellation_total_dplc_sum" readonly="readonly" class="<?php if ($lieu->getTotalDPLC()) echo 'alerte'; ?>" value="Σ <?php echo $lieu->getTotalDPLC() ?>"/>
   </li>
            <?php endif; ?>

        </ul>
    </div>
</div>