<ul class="sous_onglets">
    <?php foreach($items as $key => $lieu): ?>
        <li class="<?php if ($produit->getLieu()->getHash() == $lieu->getHash()): ?>ui-tabs-selected<?php endif; ?>">
            <a href="<?php echo url_for('dr_recolte_noeud', array('id' => $produit->getDocument()->_id, 'hash' => $lieu->getHash())) ?>">
				<?php if($lieu->getMention()->getKey() != "mention"): ?>
					<?php echo ucfirst(preg_replace("/(AOC|Alsace) /", "", str_replace("Grands Crus", "Grd Cru ", $lieu->getAppellation()->getLibelle()))) ?>
				<?php endif; ?>
				<?php echo $lieu->getLibelle() ?>
			</a>
        </li>
    <?php endforeach; ?>

    <?php if (!$declaration->recolte->getNoeudAppellations()->get($appellation_key)->hasAllDistinctLieu() && $produit->getMention()->getKey() == "mention"): ?>
        <li class="ajouter ajouter_lieu"><a href="#">Ajouter un lieu dit</a></li>
    <?php endif; ?>
</ul>
