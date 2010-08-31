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
            <input type="text" readonly="readonly" value="<?php echo $lieu->getTotalSuperficie() ?>" />
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

        <p class="vol_place"><input type="text" readonly="readonly" value="<?php echo $lieu->getTotalCaveParticuliere() ?>" /></p>
        <p class="vol_total_recolte"><input type="text" readonly="readonly" value="<?php echo $lieu->getTotalVolume() ?>" /></p>
        <ul class="vol_revendique_dplc">
            <li class="rendement">Rdt : <strong><?php echo $lieu->getRendementRecoltant() ?> hl/ha</strong></li>
            <?php if ($lieu->hasRendement()): ?>
                <?php if ($lieu->hasRendementAppellation()): ?>
                    <li><input type="text" readonly="readonly" value="<?php echo $lieu->getVolumeRevendiqueAppellation() ?>" /></li>
                    <li><input type="text" readonly="readonly" class="alerte" value="<?php echo $lieu->getDPLCAppellation() ?>"/></li>
                <?php endif; ?>
                <li><input type="text" readonly="readonly" value="Σ <?php echo $lieu->getTotalVolumeRevendique() ?>" /></li>
                <li><input type="text" readonly="readonly" class="alerte" value="Σ <?php echo $lieu->getTotalDPLC() ?>"/></li>
            <?php endif; ?>

        </ul>
    </div>
</div>