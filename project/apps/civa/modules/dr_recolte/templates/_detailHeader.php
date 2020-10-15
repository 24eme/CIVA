<div id="colonne_intitules">

    <?php if ($produit->getAppellation()->getConfig()->hasLieuEditable()): ?>
        <p class="lieu">
            Lieu-dit <a href="" class="msg_aide" rel="help_popup_DR_lieu-dit" title="Message aide"></a>
        </p>
    <?php endif; ?>

    <p class="denomination">
        <?php if ($produit->getConfig()->hasDenomination()) : ?>
            Dénom. complémentaire <a href="" class="msg_aide" rel="help_popup_DR_denomination" title="Message aide"></a>&nbsp;
        <?php else: ?>
            &nbsp;<br />&nbsp;
        <?php endif; ?>
    </p>

    <p class="mention">
        <?php if ($produit->getConfig()->hasVtsgn()) : ?>
            Mention VT/SGN <a href="" class="msg_aide" rel="help_popup_DR_mention" title="Message aide"></a>
        <?php endif; ?>&nbsp;
    </p>

    <p class="superficie">Superficie <span class="unites">(ares)</span><a href="" class="msg_aide" rel="help_popup_DR_superficie" title="Message aide"></a> </p>
    <?php if (!$produit->getConfig()->hasNoNegociant()): ?>
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

        <?php if (!$produit->getConfig()->hasNoCooperative()): ?>
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

        <?php if ($has_acheteurs_mout && !$produit->getConfig()->hasNoMout()): ?>
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
        <ul class="vol_revendique_dplc">
            <?php if ($produit->getLieu()->getConfig()->existRendement()): ?>
            <li>Volume revendiqué <span class="unites">(hl)</span> <a href="" class="msg_aide" rel="help_popup_DR_vol_revendique" title="Message aide"></a></li>
            <li>Volume à détruire <span class="unites">(hl)</span> <a href="" class="msg_aide" rel="help_popup_DR_usages_industriels" title="Message aide"></a></li>
            <?php endif; ?>
            <?php if ($produit->canHaveVci()): ?>
                <li>VCI <span class="unites">(hl)</span> <a href="" class="msg_aide" rel="help_popup_DR_vci" title="Message aide"></a></li>
            <?php endif; ?>
        </ul>
    <ul>
        <?php if ($produit->getLieu()->getConfig()->existRendementAppellation() || $produit->getLieu()->getConfig()->existRendementCouleur()):?>
        <li>
            Dépassement
            <a href="" class="msg_aide" rel="help_popup_DR_dplc" title="Message aide"></a></li>
        <?php endif; ?>
        <?php if ($produit->getConfig()->existRendementCepage()):?>
        <li>
            Dépassement Cépage
            <?php if (!($produit->getLieu()->getConfig()->existRendementAppellation() || $produit->getLieu()->getConfig()->existRendementCouleur())): ?>
            <a href="" class="msg_aide" rel="help_popup_DR_dplc" title="Message aide"></a>
            <?php endif; ?>
        </li>
        <?php endif; ?>
    </ul>
</div>
