<?php if ($produit->getAppellation()->getConfig()->hasManyLieu()): ?>
<ul class="sous_onglets">
	<?php $first = true ?>
    <?php foreach($produit->getAppellation()->getLieux() as $key => $lieu): ?>
        <?php if ($key != 'lieu'): ?>
        <li class="<?php if ($produit->getLieu()->getHash() == $lieu->getHash()): ?>ui-tabs-selected<?php endif; ?> <?php if ($first):?>premier<?php endif; ?>">
            <a href=""><?php echo $lieu->getConfig()->libelle ?></a>
        </li>
		<?php $first = false ?>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if (!$declaration->recolte->getNoeudAppellations()->get($appellation_key)->hasAllDistinctLieu()): ?>
        <li class="ajouter ajouter_lieu"><a href="#">Ajouter un lieu dit</a></li>
    <?php endif; ?>
<?php endif; ?>

<?php /*if($onglets->getCurrentLieu()->isInManyMention()) : ?>
<?php include_partial('ongletsMentions', array('declaration' => $declaration,
        'appellation_key' => $appellation_key,
        'lieu' => $lieu,
        'onglets' => $onglets)); ?>
<?php endif; */ ?>

</ul>
