<ul id="onglets_majeurs" class="clearfix onglets_courts">
    <?php foreach($appellations as $key => $appellation): ?>
        <li <?php if ($appellation_current_key == $key): ?>class="ui-tabs-selected"<?php endif; ?>><a href="#"><?php echo str_replace('AOC', '<span>AOC</span> <br />',$appellations_config->get($key)->libelle) ?></a></li>
    <?php endforeach; ?>
</ul>