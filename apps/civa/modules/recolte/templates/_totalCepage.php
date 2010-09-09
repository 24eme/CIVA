<div id="col_cepage_total" class="col_recolte col_total">
    <h2>Total <br /> <?php echo $onglets->getCurrentCepage()->getConfig()->libelle ?></h2>

    <div class="col_cont">
        <p class="denomination">&nbsp;</p>
        <p class="mention">&nbsp;</p>
        <p class="superficie">
            <input type="hidden" id="cepage_total_superficie_orig" value="<?php echo $cepage->getTotalSuperficie() ?>" />
            <input type="text" id="cepage_total_superficie" readonly="readonly" value="<?php echo $cepage->getTotalSuperficie() ?>" />
        </p>

        <div class="vente_raisins">
                <?php
                 if (!$onglets->getCurrentCepage()->getConfig()->hasNoNegociant()){
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                                                              'acheteurs_value' => $cepage->getTotalAcheteursByCvi('negoces')));
                 } ?>
            &nbsp;
        </div>

        <div class="caves">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                                                              'acheteurs_value' => $cepage->getTotalAcheteursByCvi('cooperatives')))
                ?>
        </div>

        <?php if ($has_acheteurs_mout): ?>
        <div class="mouts">
            <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                                                              'acheteurs_value' => $cepage->getTotalAcheteursByCvi('mouts')))
                ?>
        </div>
        <?php endif; ?>

        <p class="vol_place">
   <input type="hidden" id='cepage_total_cave_orig' value="<?php echo $cepage->getTotalCaveParticuliere() ?>" />
   <input type="text" id='cepage_total_cave' readonly="readonly" value="<?php echo $cepage->getTotalCaveParticuliere() ?>" />
   </p>
        <p class="vol_total_recolte">
   <input type="hidden" id='cepage_max_volume' value="<?php echo $cepage->getVolumeMax() ?>" />
   <input type="hidden" id='cepage_total_volume_orig' value="<?php echo $cepage->getTotalVolume() ?>" />
   <input type="text" id='cepage_total_volume' readonly="readonly" value="<?php echo $cepage->getTotalVolume() ?>" />
   </p>
   <ul class="vol_revendique_dplc">
   <li class="rendement">Rdt : <strong><span id="cepage_current_rendement"><?php echo $cepage->getRendementRecoltant() ?></span> hl/ha</strong></li>
   <?php if ($cepage->hasRendement()): ?>
	   <li>
	      <input type="text" id="cepage_volume_revendique" readonly="readonly" value="<?php echo $cepage->getTotalVolumeRevendique() ?>" />
	      <input type="hidden" id="cepage_volume_revendique_orig" value="<?php echo $cepage->getTotalVolumeRevendique() ?>" />
	      </li>
	      <li>
	      <input type="text" id="cepage_volume_dplc" readonly="readonly" class="<?php if ($cepage->getTotalDPLC()) echo 'alerte'; ?>" value="<?php echo $cepage->getTotalDPLC() ?>" />
	      <input type="hidden" id="cepage_volume_dplc_orig" class="<?php if ($cepage->getTotalDPLC()) echo 'alerte'; ?>" value="<?php echo $cepage->getTotalDPLC() ?>" />
	      </li>
	      <?php endif; ?>
        </ul>
    </div>
</div>
