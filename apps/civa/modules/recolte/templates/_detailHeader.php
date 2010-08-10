<div id="colonne_intitules">
    <ul class="denomination_mention">
        <li>Dénomination complémentaire</li>
        <li>Mention VT/SGN</li>

    </ul>

    <p class="superficie">Superficie</p>


    <div class="vente_raisins">
        <?php include_partial('detailHeaderAcheteurs', array('title' => "Ventes de Raisins",
                                                             'acheteurs' => $acheteurs_negoce,
                                                             'list_acheteurs' => $list_acheteurs_negoce
                                                            )) ?>
    </div>


    <div class="caves">
        <?php include_partial('detailHeaderAcheteurs', array('title' => "Caves Coopératives",
                                                             'acheteurs' => $acheteurs_cave,
                                                             'list_acheteurs' => $list_acheteurs_cave
                                                            )) ?>
    </div>

    <?php if ($has_acheteurs_mout): ?>
    <div class="caves">
        <?php include_partial('detailHeaderAcheteurs', array('title' => "Acheteurs de Mouts",
                                                             'acheteurs' => $acheteurs_mout,
                                                             'list_acheteurs' => $list_acheteurs_mout
                                                            )) ?>
    </div>
    <?php endif; ?>

    <p class="vol_place">Volume sur place</p>

    <p class="vol_total_recolte">Volume Total Récolté</p>

    <ul class="vol_revendique_dplc">
        <li>Volume revendiqué</li>
        <li>DPLC</li>
    </ul>
</div>