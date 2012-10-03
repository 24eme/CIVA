<ul id="liste_sepages">
    <?php foreach ($onglets->getItemsCouleur() as $key_couleur => $couleur): ?>
        <?php foreach ($couleur->getConfig()->getCepages() as $key => $cepage): ?>
            <?php if (!$recapitulatif && $onglets->getCurrentKeyCepage() == $key): ?>
                <li class="ui-tabs-selected">
                    <a href="<?php echo url_for($onglets->getUrl('recolte',null, null, $key_couleur, $key)->getRawValue()) ?>">
                        <?php echo $cepage->libelle ?>
                        <?php if ($nb_details_current && $nb_details_current > 0): ?>
                            &nbsp;<span>(<?php echo $nb_details_current ?>)</span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?php echo url_for($onglets->getUrl('recolte', null, null, $key_couleur, $key)->getRawValue()) ?>">
                        <?php echo $cepage->libelle ?>
                        <?php if ($couleur->exist($key) && $couleur->get($key)->detail->count() > 0): ?>
                            &nbsp;<span>(<?php echo $couleur->get($key)->detail->count() ?>)</span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <!--<li class="alerte"><a href="#">Rebêche <span></span></a></li>-->
    <li class="recapitulatif <?php if ($recapitulatif): ?> ui-tabs-selected<?php endif; ?>" ><a href="<?php echo url_for($onglets->getUrlRecap()->getRawValue()) ?>">Recapitulatif des sorties<span></span></a></li>
</ul>