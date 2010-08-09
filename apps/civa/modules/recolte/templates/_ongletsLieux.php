<?php if (count($onglets->getItemsLieu()) > 0): ?>
<ul id="" class="clearfix onglets_courts">
    <?php foreach($onglets->getItemsLieu($appellation_key) as $key => $lieu): ?>
        <?php if ($key != 'lieu'): ?>
        <li <?php if ($onglets->getCurrentKeyAppellation() == $appellation_key && $onglets->getCurrentKeyLieu() == $key): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href="<?php echo url_for($onglets->getUrl('recolte', $appellation_key, $key)->getRawValue()) ?>"><?php echo $configuration->get($lieu->getHash())->libelle ?></a>
        </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
<?php endif; ?>