<div id="colonne_intitules">
    <p class="denomination">
<?php if ($onglets->getCurrentCepage()->getConfig()->hasDenomination()) : ?>
   Dénom. complémentaire
<?php endif; ?>&nbsp;
   </p>

    <p class="mention">
<?php if ($onglets->getCurrentCepage()->getConfig()->hasVtsgn()) : ?>
   Mention VT/SGN
<?php endif; ?>&nbsp;
   </p>

    <p class="superficie">Superficie</p>
    <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoNegociant()): ?>
    <div class="vente_raisins">
        <?php
            include_partial('headerAcheteurs', array('title' => "Ventes de Raisins",
                'acheteurs' => $acheteurs->negoces,
                'list_acheteurs' => ListAcheteursConfig::getNegoces()
        )); ?>
    </div>
    <?php endif; ?>

    <div class="caves">
        <?php
        include_partial('headerAcheteurs', array('title' => "Caves Coopératives",
            'acheteurs' => $acheteurs->cooperatives,
            'list_acheteurs' => ListAcheteursConfig::getCooperatives()
        )) ?>
    </div>

    <?php if ($has_acheteurs_mout): ?>
        <div class="mouts">
        <?php
            include_partial('headerAcheteurs', array('title' => "Acheteurs de Mouts",
                'acheteurs' => $acheteurs->mouts,
                'list_acheteurs' => ListAcheteursConfig::getMouts()
            ))
        ?>
        </div>
    <?php endif; ?>

    <p class="vol_place">Volume sur place</p>
    <p class="vol_total_recolte">Volume Total Récolté</p>
    <?php if ($onglets->getCurrentLieu()->hasRendement()): ?>
    <ul class="vol_revendique_dplc">
        <li>Volume revendiqué</li>
        <li>DPLC</li>
    </ul>
    <?php endif; ?>
</div>