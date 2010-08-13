<ul id="onglets_majeurs" class="clearfix onglets_recolte">
    <?php foreach($onglets->getItemsAppellation() as $key => $appellation): ?>
        <li <?php if ($onglets->getCurrentKeyAppellation() == $key): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href="<?php echo url_for($onglets->getUrl('recolte', $key)->getRawValue()) ?>"><?php echo str_replace('AOC', '<span>AOC</span> <br />',$appellation->getConfig()->libelle) ?></a>
            <?php include_partial('ongletsLieux', array('declaration' => $declaration,
                                                        'appellation_key' => $key,
                                                        'onglets' => $onglets)); ?>
        </li>
    <?php endforeach; ?>
        <li class="ajouter"><a href="#">Ajouter<br /> une appelation</a></li>
</ul>