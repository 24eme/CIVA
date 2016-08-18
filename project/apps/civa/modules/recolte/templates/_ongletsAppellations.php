<ul id="onglets_majeurs" class="clearfix onglets_recolte">
    <?php foreach($onglets->getItemsAppellationConfig() as $key => $appellation): ?>
        <?php if ($declaration->exist(HashMapper::inverse($appellation->getHash()))): ?>
        <li <?php if ($onglets->getCurrentAppellation()->getHash() == HashMapper::inverse($appellation->getHash())): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href="<?php echo url_for($onglets->getUrl('recolte', $key)->getRawValue()) ?>"><?php echo str_replace('AOC', '<span>AOC</span> <br />',$appellation->libelle) ?></a>
            <?php if ($onglets->getCurrentAppellation()->getConfig()->hasManyLieu() && $onglets->getCurrentAppellation()->getHash() == HashMapper::inverse($appellation->getHash())): ?>
            <?php include_partial('ongletsLieux', array('declaration' => $declaration,
                                                        'appellation_key' => $onglets->getCurrentAppellation()->getKey(),
                                                        'onglets' => $onglets)); ?>
            <?php endif; ?>
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
        <?php if (!$declaration->recolte->getNoeudAppellations()->hasAllAppellation()): ?>
        <li class="ajouter ajouter_appelation"><a href="#">Ajouter une<br /> appellation</a></li>
        <?php endif; ?>
</ul>
