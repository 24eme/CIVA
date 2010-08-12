<div id="colonne_intitules" style="margin-left: 2px;">
    <ul class="denomination_mention">
        <li>&nbsp;</li>
        <li>&nbsp;</li>

    </ul>

    <p class="superficie"><?php echo $cepage->getTotalSuperficie() ?></p>

    <div class="vente_raisins">
        <?php include_partial('totalCepageAcheteurs', array('title' => "Ventes de Raisins",
                                                             'acheteurs' => $acheteurs_negoce,
                                                             'acheteurs_value' => $cepage->getTotalAcheteursByCvi('negoces'))) ?>
    </div>

    <div class="caves">
        <?php include_partial('totalCepageAcheteurs', array('title' => "Caves Coopératives",
                                                             'acheteurs' => $acheteurs_cave,
                                                             'acheteurs_value' => $cepage->getTotalAcheteursByCvi('cooperatives'))) ?>
    </div>

    <?php if ($has_acheteurs_mout): ?>
    <div class="caves">
        <?php include_partial('totalCepageAcheteurs', array('title' => "Acheteurs de Mouts",
                                                             'acheteurs' => $acheteurs_mouts,
                                                             'acheteurs_value' => $cepage->getTotalAcheteursByCvi('mouts'))) ?>
    </div>
    <?php endif; ?>

    <p class="vol_place"><?php echo $cepage->getTotalCaveParticuliere() ?>&nbsp;</p>

    <p class="vol_total_recolte"><?php echo $cepage->getTotalVolume() ?>&nbsp;</p>

    <ul class="vol_revendique_dplc">
        <li><?php echo $cepage->getTotalVolumeRevendique() ?>&nbsp;</li>
        <li><?php echo $cepage->getTotalDPLC() ?>&nbsp;</li>
    </ul>
</div>
