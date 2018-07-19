<?php $is_appellations_completes = count($ds->declaration->getAppellationsSorted()) >= count($ds->declaration->getConfig()->getArrayAppellations()) ?>

<ul id="onglets_majeurs" class="clearfix onglets_stock <?php echo ($is_appellations_completes) ? "concis" : null ?>">
    <?php foreach ($ds->declaration->getAppellationsSorted() as $app_key => $app):  ?>
    <?php $selected = (isset($appellation) && $app->getHash() == $appellation->getHash()); ?>
    <li class="<?php echo $selected ? 'ui-tabs-selected' : '' ; ?>">
        <a class="ajax" href="<?php echo url_for('ds_edition_operateur', $app->getRawValue()); ?>">
            <span><?php echo (preg_match('/^AOC/', $app->libelle))? 'AOC ' : ''; ?></span>
            <br><?php echo (preg_match('/^AOC/', $app->libelle))? substr($app->libelle, 4) : $app->libelle; ?>
        </a>
        <?php if(isset($appellation) && $selected && $appellation->getRawValue() instanceof DSAppellation && count($appellation->getConfig()->mentions->getFirst()->getLieux()) > 1): ?>
            <ul class="sous_onglets" style="position: absolute; left: auto;">
                <?php foreach ($appellation->getLieuxSorted() as $l): ?>
                <li class="<?php echo (isset($lieu) && $lieu->getHash() == $l->getHash())? 'ui-tabs-selected' : ''; ?>">
                    <a class="ajax" href="<?php echo url_for('ds_edition_operateur', $l) ?>"><?php echo $l->getLibelle(); ?></a>
                </li>
                <?php endforeach; ?>
                <li class="ajouter ajouter_lieu"><a class="ajax" href="<?php echo url_for('ds_ajout_lieu', $appellation) ?>">&nbsp;Ajouter un lieu-dit</a></li>
            </ul>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>

    <?php if(!$is_appellations_completes): ?>
	<li class="ajouter ajouter_appelation"><a class="ajax" title="Ajouter une appellation" href="<?php echo url_for('ds_ajout_appellation', $ds) ?>">&nbsp;</a></li>
    <?php endif; ?>
    <li class="recap_stock <?php echo isset($recap) ? 'ui-tabs-selected' : '' ; ?>">
        <a class="ajax" href="<?php echo url_for("ds_recapitulatif_lieu_stockage", $ds); ?>" style="height: 30px;">
        Récap. lieu<br />stockage
        </a>
    </li>
</ul>
