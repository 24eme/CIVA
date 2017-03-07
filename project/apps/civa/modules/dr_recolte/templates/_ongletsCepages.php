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
    <?php if(!$recapitulatif && $produit->getMention()->getKey() != "mention" && $produit->getAppellation()->getMentions()->exist('mention')): ?>
    <li class="raccourci_mention"><a href="<?php echo url_for('dr_recolte_correspondance_mention', array('id' => $produit->getDocument()->_id, 'hash' => $produit->getHash(), 'mention' => 'mention')) ?>">Hors&nbsp;mention</a></li>
    <?php endif; ?>
    <?php if(!$recapitulatif && $produit->getMention()->getKey() != "mentionVT" && $produit->getAppellation()->getMentions()->exist('mentionVT/'.$produit->getLieu()->getKey())): ?>
    <li class="raccourci_mention"><a href="<?php echo url_for('dr_recolte_correspondance_mention', array('id' => $produit->getDocument()->_id, 'hash' => $produit->getHash(), 'mention' => 'mentionVT')) ?>">Mention VT</a></li>
    <?php endif; ?>
    <?php if(!$recapitulatif && $produit->getMention()->getKey() != "mentionSGN" && $produit->getAppellation()->getMentions()->exist('mentionSGN/'.$produit->getLieu()->getKey())): ?>
    <li class="raccourci_mention"><a href="<?php echo url_for('dr_recolte_correspondance_mention', array('id' => $produit->getDocument()->_id, 'hash' => $produit->getHash(), 'mention' => 'mentionSGN')) ?>">Mention SGN</a></li>
    <?php endif; ?>

    <!--<li class="alerte"><a href="#">Rebêche <span></span></a></li>-->
    <li class="recapitulatif <?php if ($recapitulatif): ?> ui-tabs-selected<?php endif; ?>" ><a href="<?php echo url_for("dr_recolte_recapitulatif", array('sf_subject' => $produit->getDocument(), "hash" => $produit->getLieu()->getHash())) ?>">Récap. des<br />Ventes&nbsp;et<br />Vol.&nbsp;à&nbsp;détruire<span></span></a></li>
</ul>
