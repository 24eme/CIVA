<?php use_helper('Float') ?>
<div id="col_couleur_totale_alternatif" class="col_recolte col_total col_calcule <?php echo (count($produit->getLieu()->getConfig()->getCouleurs()) > 1) ? "col_double" : "" ?>">
    <h2>Total
        <?php echo $produit->getAppellation()->getConfig()->libelle ?>
        <strong><?php echo $couleur->getLibelle() ?></strong>
        <a href="" class="msg_aide" rel="help_popup_DR_total_couleur_alternatif" title="Message aide"></a>
    </h2>

    <div class="col_cont">
        <?php if ($produit->getAppellation()->getConfig()->hasLieuEditable()): ?>
            <p class="lieu">&nbsp;</p>
        <?php endif; ?>
        <p class="denomination">&nbsp;</p>
        <p class="mention">&nbsp;</p>
        <p class="superficie">
            <input type="text" readonly="readonly" value="<?php echoFloat($couleur->getTotalSuperficie()); ?>" />
        </p>

        <?php if (!$produit->getConfig()->hasNoNegociant()): ?>
            <div class="vente_raisins">
                <?php include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->negoces,
                    'acheteurs_value' => $couleur->getVolumeAcheteurs('negoces'))); ?>
            </div>
        <?php endif; ?>

        <?php if (!$produit->getConfig()->hasNoCooperative()): ?>
            <div class="caves">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->cooperatives,
                    'acheteurs_value' => $couleur->getVolumeAcheteurs('cooperatives')))
                ?>
            </div>
        <?php endif; ?>

        <?php if ($has_acheteurs_mout && !$produit->getConfig()->hasNoMout()): ?>
            <div class="mouts">
                <?php
                include_partial('itemAcheteurs', array('acheteurs' => $acheteurs->mouts,
                    'acheteurs_value' => $couleur->getVolumeAcheteurs('mouts')))
                ?>
            </div>
        <?php endif; ?>

        <p class="vol_place">
            <input type="text" readonly="readonly" value="<?php echoFloat($couleur->getTotalCaveParticuliere()); ?>" />
        </p>
        <p class="vol_total_recolte">
            <input type="text" readonly="readonly" value="<?php echoFloat($couleur->getTotalVolume()); ?>" />
        </p>
        <ul class="vol_revendique_dplc">
            <li class="rendement <?php if (round($couleur->getRendementRecoltant()) > round($couleur->getRendementMax())): echo 'rouge'; endif;?>">
                Rdt : <strong><span><?php echo round($couleur->getRendementRecoltant(), 0); ?></span>&nbsp;hl/ha</strong>
                <span class="picto_rdt_aide_col_total">
                    <a href="" class="msg_aide" rel="help_popup_DR_total_appellation" title="Message aide"></a>
                </span>
            </li>
            <li>
                <input type="text"readonly="readonly" class="<?php if ($couleur->getDplc() > 0) echo 'rouge'; ?>" value="<?php echoFloat($couleur->getVolumeRevendique()); ?>" />
            </li>
            <li>
                <input type="text" readonly="readonly" class="<?php if ($couleur->getDplc() > 0) echo 'rouge'; ?>" value="<?php echoFloat($couleur->getUsagesIndustriels()); ?>"/>
            </li>
            <?php if($couleur->canHaveVci()): ?>
            <li>
                <input class="num" type="text" readonly="readonly" value="<?php echoFloat($couleur->getTotalVci()); ?>" />
            </li>
            <?php endif; ?>
        </ul>
        <ul>
            <?php if ($couleur->getConfig()->hasRendementNoeud()):?>
            <li>
                <input type="text" class="num <?php if ($couleur->getDplcRendement() > 0) echo 'rouge'; ?> <?php if ($couleur->getDplcRendement() > 0 && $couleur->getDplc() == $couleur->getDplcRendement()) echo 'alerte'; ?>" readonly="readonly" value="<?php echoFloat($couleur->getDplcRendement()); ?>"/>
            </li>
            <?php endif; ?>
            <?php if ($couleur->getConfig()->existRendementCepage()):?>
            <li>
                <input type="text" class="num <?php if ($couleur->getDplcTotal() > 0) echo 'rouge'; ?> <?php if ($couleur->getDplcTotal() > 0 && $couleur->getDplc() == $couleur->getDplcTotal()) echo 'alerte'; ?>" value="<?php echoFloat($couleur->getDplcTotal()); ?>"/>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
