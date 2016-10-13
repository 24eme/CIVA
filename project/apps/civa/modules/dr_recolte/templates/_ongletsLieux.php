<ul class="sous_onglets">
	<?php $first = true ?>
    <?php foreach($items as $key => $lieu): ?>
        <li class="<?php if ($produit->getLieu()->getHash() == $lieu->getHash()): ?>ui-tabs-selected<?php endif; ?> <?php if ($first):?>premier<?php endif; ?>">
            <a href="<?php echo url_for('dr_recolte_noeud', array('id' => $produit->getDocument()->_id, 'hash' => $lieu->getHash())) ?>"><?php if($lieu->getMention()->getKey() != "mention"): ?><?php echo $lieu->getAppellation()->getLibelle() ?> <?php endif; ?><?php echo $lieu->getLibelle() ?></a>
        </li>
		<?php $first = false ?>
    <?php endforeach; ?>

    <?php if (!$declaration->recolte->getNoeudAppellations()->get($appellation_key)->hasAllDistinctLieu()): ?>
        <li class="ajouter ajouter_lieu"><a href="#">Ajouter un lieu dit</a></li>
    <?php endif; ?>
</ul>
