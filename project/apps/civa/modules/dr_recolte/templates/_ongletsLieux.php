<?php if ($produit->getMention()->getConfig()->hasManyLieu()): ?>
<ul class="sous_onglets">
	<?php $first = true ?>
    <?php foreach($produit->getMention()->getLieux() as $key => $lieu): ?>
        <?php if ($key != 'lieu'): ?>
        <li class="<?php if ($produit->getLieu()->getHash() == $lieu->getHash()): ?>ui-tabs-selected<?php endif; ?> <?php if ($first):?>premier<?php endif; ?>">
            <a href="<?php echo url_for('dr_recolte_noeud', array('id' => $produit->getDocument()->_id, 'hash' => HashMapper::inverse($lieu->getHash()))) ?>"><?php echo $lieu->getConfig()->libelle ?></a>
        </li>
		<?php $first = false ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!$declaration->recolte->getNoeudAppellations()->get($appellation_key)->hasAllDistinctLieu()): ?>
        <li class="ajouter ajouter_lieu"><a href="#">Ajouter un lieu dit</a></li>
    <?php endif; ?>
<?php endif; ?>
</ul>
