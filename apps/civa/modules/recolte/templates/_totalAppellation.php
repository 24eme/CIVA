<div id="col_recolte_totale" class="col_recolte col_total">
    <h2>Total <?php echo $configuration->get($onglets->getItemsLieu()->getHash())->libelle ?></h2>

    <div class="col_cont">
        <p class="superficie">
            <input type="text" readonly="readonly" value="<?php echo $lieu->getTotalSuperficie() ?>" />
        </p>

        <div class="vente_raisins">
                <?php
                include_partial('totalCepageAcheteurs', array('acheteurs' => $acheteurs_negoce,
                                                              'acheteurs_value' => $lieu->getTotalAcheteursByCvi('negoces')))
                ?>
        </div>

        <div class="caves">
            <?php
                include_partial('totalCepageAcheteurs', array('acheteurs' => $acheteurs_cave,
                                                              'acheteurs_value' => $lieu->getTotalAcheteursByCvi('cooperatives')))
                ?>
        </div>

        <?php if ($has_acheteurs_mout): ?>
        <div class="mouts">
            <?php
                include_partial('totalCepageAcheteurs', array('acheteurs' => $acheteurs_mouts,
                                                              'acheteurs_value' => $lieu->getTotalAcheteursByCvi('mouts')))
                ?>
        </div>
        <?php endif; ?>

        <p class="vol_place"><input type="text" readonly="readonly" value="<?php echo $lieu->getTotalCaveParticuliere() ?>" /></p>
        <p class="vol_total_recolte"><input type="text" readonly="readonly" value="<?php echo $lieu->getTotalVolume() ?>" /></p>
        <ul class="vol_revendique_dplc">
            <li class="rendement">Rdt : <strong><?php echo $lieu->getRendementRecoltant() ?> hl/ha</strong></li>
            <li><input type="text" readonly="readonly" value="<?php echo $lieu->getTotalVolumeRevendique() ?>" /></li>
            <li><input type="text" readonly="readonly" class="alerte" value="<?php echo $lieu->getTotalDPLC() ?>"/></li>
        </ul>
    </div>
</div>