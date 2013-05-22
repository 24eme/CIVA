<ul id="onglets_majeurs" class="clearfix onglets_stock">
    <?php foreach ($noeud->getDocument()->declaration->getAppellationsSorted() as $app_key => $app):  ?>
    
    <?php $selected = ($app->getHash() == $noeud->getAppellation()->getHash()); ?>
    <li class="<?php echo $selected ? 'ui-tabs-selected' : '' ; ?>">
        <a href="<?php echo url_for('ds_edition_operateur', $app->getRawValue()); ?>">
            <span><?php echo (preg_match('/^AOC/', $app->libelle))? 'AOC ' : ''; ?></span> 
            <br><?php echo (preg_match('/^AOC/', $app->libelle))? substr($app->libelle, 4) : $app->libelle; ?>
        </a>
        
        <?php if($selected && $noeud->getAppellation()->getConfig()->hasManyLieu()): ?>
        <?php $has_lieux = true; ?>
            <ul class="sous_onglets">
              <?php foreach ($noeud->getAppellation()->getLieux() as $lieu_key => $lieu): ?>
              <li class="<?php echo ($noeud->getHash() == $lieu->getHash())? 'ui-tabs-selected' : ''; ?>">
                  <a href="<?php echo url_for('ds_edition_operateur', $lieu) ?>"><?php echo $lieu->getConfig()->getLibelle(); ?></a></li>
              <?php endforeach; ?>
                <li class="ajouter ajouter_lieu"><a href="#">Ajouter un lieu dit</a></li>
            </ul>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
    <li>
        <a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id)); ?>" style="height: 30px;">
        <br>Récapitulatif</a>
    </li>
</ul>