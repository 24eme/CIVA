<ul id="onglets_majeurs" class="clearfix onglets_recolte">
    <?php foreach($declaration->recolte->getConfig()->getArrayAppellations() as $appellation): ?>
        <?php if ($declaration->exist(HashMapper::inverse($appellation->getHash()))): ?>
        <li <?php if ($produit->getAppellation()->getHash() == HashMapper::inverse($appellation->getHash())): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href=""><?php echo str_replace('AOC', '<span>AOC</span> <br />',$appellation->libelle) ?></a>
            <?php if ($produit->getAppellation()->getConfig()->hasManyLieu() && $produit->getAppellation()->getHash() == HashMapper::inverse($appellation->getHash())): ?>
            <?php include_partial('ongletsLieux', array('declaration' => $declaration,
                                                        'appellation_key' => $produit->getAppellation()->getKey())); ?>
            <?php endif; ?>
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
        <?php if (!$declaration->recolte->getNoeudAppellations()->hasAllAppellation()): ?>
        <li class="ajouter ajouter_appelation"><a href="#">Ajouter une<br /> appellation</a></li>
        <?php endif; ?>
</ul>
