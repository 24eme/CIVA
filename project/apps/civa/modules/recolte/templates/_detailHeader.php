<div id="colonne_intitules">

    <?php if ($onglets->getCurrentAppellation()->getConfig()->hasLieuEditable()): ?>
        <p class="lieu">
            Lieu-dit <a href="" class="msg_aide" rel="help_popup_DR_lieu-dit" title="Message aide"></a>
        </p>
    <?php endif; ?>

    <p class="denomination">
        <?php if ($onglets->getCurrentCepage()->getConfig()->hasDenomination()) : ?>
            Dénom. complémentaire <a href="" class="msg_aide" rel="help_popup_DR_denomination" title="Message aide"></a>&nbsp;
        <?php else: ?>
            &nbsp;<br />&nbsp;
        <?php endif; ?>
    </p>

    <p class="mention">
        <?php if ($onglets->getCurrentCepage()->getConfig()->hasVtsgn()) : ?>
            Mention VT/SGN <a href="" class="msg_aide" rel="help_popup_DR_mention" title="Message aide"></a>
        <?php endif; ?>&nbsp;
    </p>

    <p class="superficie">Superficie <span class="unites">(ares)</span><a href="" class="msg_aide" rel="help_popup_DR_superficie" title="Message aide"></a> </p>
    <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoNegociant()): ?>
        <div class="vente_raisins">
            <?php
            include_partial('headerAcheteurs', array('title' => "Ventes de Raisins",
                'acheteurs' => $acheteurs->negoces,
                'list_acheteurs' => ListAcheteursConfig::getNegoces(),
                'var_rel_help' => 'help_popup_DR_vente_raisins'
            ));
            ?>
        </div>
    <?php endif; ?>

        <?php if (!$onglets->getCurrentCepage()->getConfig()->hasNoCooperative()): ?>
        <div class="caves">
            <?php
            include_partial('headerAcheteurs', array('title' => "Caves Coopératives",
                'acheteurs' => $acheteurs->cooperatives,
                'list_acheteurs' => ListAcheteursConfig::getCooperatives(),
                'var_rel_help' => 'help_popup_DR_caves'
            ))
            ?>
        </div>
    <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$onglets->getCurrentCepage()->getConfig()->hasNoMout()): ?>
        <div class="mouts">
            <?php
            include_partial('headerAcheteurs', array('title' => "Acheteurs de Mouts",
                'acheteurs' => $acheteurs->mouts,
                'list_acheteurs' => ListAcheteursConfig::getMouts(),
                'var_rel_help' => 'help_popup_DR_mouts'
            ))
            ?>
        </div>
<?php endif; ?>

    <p class="vol_place">Volume sur place <span class="unites">(hl)</span><a href="" class="msg_aide" rel="help_popup_DR_vol_place" title="Message aide"></a></p>
    <p class="vol_total_recolte">Volume Total Récolté <span class="unites">(hl)</span><a href="" class="msg_aide" rel="help_popup_DR_vol_total_recolte" title="Message aide"></a></p>
    <?php if ($onglets->getCurrentLieu()->getConfig()->existRendement()): ?>
        <ul class="vol_revendique_dplc">
            <li>Volume revendiqué <span class="unites">(hl)</span> <a href="" class="msg_aide" rel="help_popup_DR_vol_revendique" title="Message aide"></a></li>
            <li>Usages industriels <span class="unites">(hl)</span> <a href="" class="msg_aide" rel="help_popup_DR_dplc" title="Message aide"></a></li>
        </ul>
    <?php endif; ?>
    <ul>
        <?php if ($onglets->getCurrentLieu()->getConfig()->existRendementAppellation() || $onglets->getCurrentLieu()->getConfig()->existRendementCouleur()):?>
        <li>Dépassement</li>
        <?php endif; ?>
        <?php if ($onglets->getCurrentCepage()->getConfig()->existRendementCepage()):?>
        <li>Dépassement Cépage</li>
        <?php endif; ?>
    </ul>
</div>