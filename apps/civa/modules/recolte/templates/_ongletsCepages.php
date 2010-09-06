<ul id="liste_sepages">
    <?php foreach($onglets->getItemsCepage() as $key => $cepage): ?>
        <li <?php if (!$recapitulatif && $onglets->getCurrentKeyCepage() == $key): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href="<?php echo url_for($onglets->getUrl('recolte', null, null, $key)->getRawValue()) ?>">
            <?php echo $cepage->libelle ?><?php if ($onglets->getCurrentLieu()->exist($key) && $onglets->getCurrentLieu()->get($key)->detail->count() > 0): ?>&nbsp;<span>(<?php echo $onglets->getCurrentLieu()->get($key)->detail->count() ?>)</span>
            <?php endif; ?>
            </a>
        </li>
    <?php endforeach; ?>
        <!--<li class="alerte"><a href="#">Rebêche <span></span></a></li>-->
        <li class="recapitulatif <?php if ($recapitulatif): ?> ui-tabs-selected<?php endif; ?>" ><a href="<?php echo url_for($onglets->getUrlRecap()->getRawValue()) ?>">Recapitulatif des ventes<span></span></a></li>
</ul>