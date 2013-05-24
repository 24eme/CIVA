<ul id="onglets_majeurs" class="clearfix onglets_stock onglets_recolte">
    <?php foreach ($ds->declaration->getAppellationsSorted() as $app_key => $app):  ?>
    <?php $selected = (isset($appellation) && $app->getHash() == $appellation->getHash()); ?>
    <li class="<?php echo $selected ? 'ui-tabs-selected' : '' ; ?>">
        <a href="<?php echo url_for('ds_edition_operateur', $app->getRawValue()); ?>">
            <span><?php echo (preg_match('/^AOC/', $app->libelle))? 'AOC ' : ''; ?></span> 
            <br><?php echo (preg_match('/^AOC/', $app->libelle))? substr($app->libelle, 4) : $app->libelle; ?>
        </a>
        
        <?php if(isset($appellation) && $selected && $appellation->getConfig()->hasManyLieu()): ?>
            <ul class="sous_onglets">
                <?php foreach ($appellation->getLieuxSorted() as $l): ?>
                <li class="<?php echo (isset($lieu) && $lieu->getHash() == $l->getHash())? 'ui-tabs-selected' : ''; ?>">
                    <a href="<?php echo url_for('ds_edition_operateur', $l) ?>"><?php echo $l->getConfig()->getLibelle(); ?></a>
                </li>
                <?php endforeach; ?>
                <li class="ajouter ajouter_lieu"><a href="<?php echo url_for('ds_ajout_lieu', $appellation) ?>">Ajouter un lieu dit</a></li>
            </ul>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
    <li class="recap_stock <?php echo isset($recap) ? 'ui-tabs-selected' : '' ; ?>">
        <a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", $ds); ?>" style="height: 30px;">
        <br>Récapitulatif</a>
    </li>
</ul>