<ul id="onglets_majeurs" class="clearfix onglets_stock">
    <?php foreach ($appellation->getDocument()->declaration->getAppellationsSorted() as $app_key => $app):  ?>
    
    <?php $selected = ($app->getHash() == $appellation->getHash()); ?>
    <li class="<?php echo $selected ? 'ui-tabs-selected' : '' ; ?>">
        <a href="<?php echo url_for('ds_edition_operateur', $app->getRawValue()); ?>">
            <span><?php echo (preg_match('/^AOC/', $app->libelle))? 'AOC ' : ''; ?></span> 
            <br><?php echo (preg_match('/^AOC/', $app->libelle))? substr($app->libelle, 4) : $app->libelle; ?>
        </a>
        
        <?php if($selected && $appellation->getConfig()->hasManyLieu()): ?>
            <ul class="sous_onglets">
                <?php foreach ($appellation->getLieux() as $l): ?>
                <li class="<?php echo (isset($lieu) && $lieu->getHash() == $l->getHash())? 'ui-tabs-selected' : ''; ?>">
                    <a href="<?php echo url_for('ds_edition_operateur', $l) ?>"><?php echo $l->getConfig()->getLibelle(); ?></a>
                </li>
                <?php endforeach; ?>
                <li class="ajouter ajouter_lieu"><a href="<?php echo url_for('ds_ajout_lieu', $appellation) ?>">Ajouter un lieu dit</a></li>
            </ul>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
    <li>
        <a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id)); ?>" style="height: 30px;">
        <br>Récapitulatif</a>
    </li>
</ul>