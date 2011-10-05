<ul id="onglets_majeurs" class="clearfix onglets_recolte">
    <?php foreach($onglets->getItemsAppellationConfig() as $key => $appellation): ?>
        <?php if ($onglets->getItemsAppellation()->exist($key)): ?>
        <li <?php if ($onglets->getCurrentKeyAppellation() == $key): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href="<?php echo url_for($onglets->getUrl('recolte', $key)->getRawValue()) ?>"><?php echo str_replace('AOC', '<span>AOC</span> <br />',$appellation->libelle) ?></a>
            <?php if ($onglets->getCurrentAppellation()->getConfig()->hasManyLieu() && $onglets->getCurrentKeyAppellation() == $key): ?>
            <?php include_partial('ongletsLieux', array('declaration' => $declaration,
                                                 'appellation_key' => $key,
                                                 'onglets' => $onglets)); ?>
            <?php endif; ?>
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
        <?php if (!$declaration->recolte->hasAllAppellation()): ?>
        <li class="ajouter ajouter_appelation"><a href="#">Ajouter une<br /> appellation</a></li>
        <?php endif; ?>
</ul>