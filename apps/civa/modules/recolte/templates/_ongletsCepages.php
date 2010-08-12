<ul id="liste_sepages">
    <?php foreach($onglets->getItemsCepage() as $key => $cepage): ?>
        <li <?php if ($onglets->getCurrentKeyCepage() == $key): ?>class="ui-tabs-selected"<?php endif; ?>>
            <a href="<?php echo url_for($onglets->getUrl('recolte', null, null, $key)->getRawValue()) ?>">
            <?php echo $cepage->libelle ?>
            <?php if ($declaration->get($onglets->getItemsCepage()->getHash())->exist($key) && $declaration->get($cepage->getHash())->detail->count() > 0): ?>
                <span>(<?php echo $declaration->get($cepage->getHash())->detail->count() ?>)</span>
            <?php endif; ?>
            </a>
        </li>
    <?php endforeach; ?>
        <!--<li class="alerte"><a href="#">Rebêche <span></span></a></li>-->
        <li class="recapitulatif"><a href="#">Recapitulatif des ventes<span></span></a></li>
</ul>