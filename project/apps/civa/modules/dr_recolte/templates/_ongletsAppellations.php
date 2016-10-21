<ul id="onglets_majeurs" class="clearfix onglets_recolte">
    <?php foreach($appellations as $appellation): ?>
        <li <?php if (preg_match("|".$appellation['hash']."/|", $produit->getMention()->getHash()."/")): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href="<?php echo url_for('dr_recolte_noeud', array('id' => $declaration->_id, 'hash' => $appellation['hash'], 'origine' => $produit->getHash())) ?>"><?php echo str_replace("Mention", "<span>Mention</span> <br />", str_replace('AOC Alsace', '<span>AOC Alsace</span> <br />',$appellation['libelle'])) ?></a>
        </li>
    <?php endforeach; ?>
        <?php if (!$declaration->hasAppellationsAvecVtsgn()): ?>
        <li class="ajouter ajouter_appelation"><a href="#">Ajouter une<br /> appellation</a></li>
        <?php endif; ?>
</ul>
<br />
<ul id="onglets_majeurs" class="clearfix onglets_recolte">
    <li class="ui-tabs-selected">
    <?php foreach($appellations as $appellation): ?>
            <?php if (count($appellation['lieux']) > 0 && preg_match("|".$appellation['hash']."/|", $produit->getMention()->getHash()."/")): ?>
            <?php include_partial('ongletsLieux', array('declaration' => $declaration,
                                                        'produit' => $produit,
                                                        'items' =>  $appellation['lieux'],
                                                        'appellation_key' => $produit->getAppellation()->getKey())); ?>
            <?php endif; ?>
    <?php endforeach; ?>
    </li>
</ul>
