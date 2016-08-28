<ul id="onglets_majeurs" class="clearfix onglets_recolte">
    <?php foreach($declaration->recolte->getMentions() as $mention): ?>
        <?php $appellation = $mention->getAppellation(); ?>
        <?php if ($declaration->exist(HashMapper::inverse($appellation->getHash()))): ?>
        <li <?php if ($produit->getMention()->getHash() == HashMapper::inverse($mention->getHash())): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href="<?php echo url_for('recolte_noeud', array('hash' => HashMapper::inverse($mention->getHash()))) ?>"><?php echo str_replace('AOC', '<span>AOC</span> <br />',$appellation->libelle) ?> <?php echo $mention->getLibelle() ?></a>
            <?php if ($produit->getAppellation()->getConfig()->hasManyLieu() && $produit->getMention()->getHash() == HashMapper::inverse($mention->getHash())): ?>
            <?php include_partial('ongletsLieux', array('declaration' => $declaration,
                                                        'produit' => $produit,
                                                        'appellation_key' => $produit->getAppellation()->getKey())); ?>
            <?php endif; ?>
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
        <?php if (!$declaration->recolte->getNoeudAppellations()->hasAllAppellation()): ?>
        <li class="ajouter ajouter_appelation"><a href="#">Ajouter une<br /> appellation</a></li>
        <?php endif; ?>
</ul>
