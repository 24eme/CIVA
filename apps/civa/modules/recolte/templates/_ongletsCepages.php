<ul id="liste_sepages">
    <?php foreach($cepages_config as $key => $cepage_config): ?>
        <li <?php if ($cepage_current_key == $key): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href="#">
            <?php echo $cepage_config->libelle ?>
            <?php if ($cepages->exist($key) && $cepages->get($key)->detail->count() > 0): ?>
                <span>(<?php echo $cepages->get($key)->detail->count() ?>)</span>
            <?php endif; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>