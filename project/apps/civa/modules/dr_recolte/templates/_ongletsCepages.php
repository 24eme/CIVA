<ul id="liste_sepages">
    <?php foreach ($produit->getLieu()->getConfig()->getCouleurs() as $key_couleur => $couleur): ?>
        <?php $couleurs_hashes = explode("/", HashMapper::inverse($couleur->getHash())); ?>
        <?php $key_couleur = $couleurs_hashes[count($couleurs_hashes) - 1]; ?>
        <?php foreach ($couleur->getChildrenNode() as $key => $cepage): ?>
            <?php if($cepage->exist('attributs/no_dr') && $cepage->get('attributs/no_dr')): continue; endif ?>
            <?php $hash = HashMapper::inverse($cepage->getHash()); ?>
            <?php if (!$recapitulatif && $produit->getHash() == $hash): ?>
                <li class="ui-tabs-selected">
                    <a href="<?php echo url_for('dr_recolte_produit', array('id' => $produit->getDocument()->_id, 'hash' => $hash)) ?>">
                        <?php echo $cepage->libelle ?> <?php echo $cepage->getMention()->getLibelle() ?><?php if ($nb_details_current && $nb_details_current > 0): ?>&nbsp;<span>(<?php echo $nb_details_current ?>)</span><?php endif; ?>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?php echo url_for('dr_recolte_produit', array('id' => $produit->getDocument()->_id, 'hash' => $hash)) ?>">
                        <?php echo $cepage->libelle ?> <?php echo $cepage->getMention()->getLibelle() ?><?php if ($produit->getDocument()->exist($hash) &&  $produit->getDocument()->get($hash)->detail->count() > 0): ?>&nbsp;<span>(<?php echo $produit->getDocument()->get($hash)->detail->count() ?>)</span><?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <!--<li class="alerte"><a href="#">Rebêche <span></span></a></li>-->
    <li class="recapitulatif <?php if ($recapitulatif): ?> ui-tabs-selected<?php endif; ?>" ><a href="<?php echo url_for("dr_recolte_recapitulatif", array('sf_subject' => $produit->getDocument(), "hash" => $produit->getLieu()->getHash())) ?>">Récap. des<br />Ventes&nbsp;et<br />Vol.&nbsp;à&nbsp;détruire<span></span></a></li>
</ul>
