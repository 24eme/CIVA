<div id="col_sepage_total" class="col_recolte col_total">
    <h2>Total AOC Alsace blanc</h2>

    <div class="col_cont">
        <p class="superficie">
            <input type="text" readonly="readonly" value="<?php echo $cepage->getTotalSuperficie() ?>" />
        </p>

        <div class="vente_raisins">
                <?php
                include_partial('totalCepageAcheteurs', array('acheteurs' => $acheteurs_negoce,
                                                              'acheteurs_value' => $cepage->getTotalAcheteursByCvi('negoces')))
                ?>
        </div>

        <div class="caves">
            <?php
                include_partial('totalCepageAcheteurs', array('acheteurs' => $acheteurs_cave,
                                                              'acheteurs_value' => $cepage->getTotalAcheteursByCvi('cooperatives')))
                ?>
        </div>

        <?php if ($has_acheteurs_mout): ?>
        <div class="mouts">
            <?php
                include_partial('totalCepageAcheteurs', array('acheteurs' => $acheteurs_mouts,
                                                              'acheteurs_value' => $cepage->getTotalAcheteursByCvi('mouts')))
                ?>
        </div>
        <?php endif; ?>

        <p class="vol_place"><input type="text" readonly="readonly" value="<?php echo $cepage->getTotalCaveParticuliere() ?>" /></p>
        <p class="vol_total_recolte"><input type="text" readonly="readonly" value="<?php echo $cepage->getTotalVolume() ?>" /></p>
        <ul class="vol_revendique_dplc">
            <li class="rendement">Rdt : <strong><?php echo $cepage->getRendementRecoltant() ?> hl/ha</strong></li>
            <li><input type="text" readonly="readonly" value="<?php echo $cepage->getTotalVolumeRevendique() ?>" /></li>
            <li><input type="text" readonly="readonly" class="alerte" value="<?php echo $cepage->getTotalDPLC() ?>" /></li>
        </ul>
    </div>
</div>
