<?php $etapesConfig = new EtapesConfig(); ?>
<?php if(!$dr->needStockageRepartition()): $etapesConfig->setDisabled('stockage'); endif; ?>

<ul id="etape_declaration" class="clearfix">
    <li class="<?php if ($etape > 1): ?>passe<?php elseif($etape == 1): ?>actif<?php endif; ?>">
        <a href="<?php echo url_for('dr_exploitation', $dr); ?>">Exploitation <em>Etape 1</em></a>
    </li>
    <li class="<?php if ($etape > 2): ?>passe<?php elseif($etape == 2): ?>actif<?php endif; ?>">
        <?php if ($dr->exist('etape') && $etapesConfig->isAutorized($dr->etape, "repartition")): ?>
            <a href="<?php echo url_for('dr_repartition', $dr); ?>">Répartition <em>Etape 2</em></a>
        <?php else: ?>
            <a href="#" onclick="return false;">Répartition <em>Etape 2</em></a>
        <?php endif; ?>
    </li>
    <li class="<?php if ($etape > 3): ?>passe<?php elseif($etape == 3): ?>actif<?php endif; ?>">
        <?php if ($dr->exist('etape') && $etapesConfig->isAutorized($dr->etape, "recolte")): ?>
            <a href="<?php echo url_for('dr_recolte', $dr); ?>">Récolte <em>Etape 3</em></a>
        <?php else: ?>
             <a href="#" onclick="return false;">Récolte <em>Etape 3</em></a>
        <?php endif; ?>
    </li>
    <li class="<?php if ($etape > 4): ?>passe<?php elseif($etape == 4): ?>actif<?php endif; ?>">
        <?php if ($dr->exist('etape') && $etapesConfig->isAutorized($dr->etape, "exploitation_autres")): ?>
            <a href="<?php echo url_for('dr_autres', $dr); ?>">Autres <em>Etape 4</em></a>
        <?php else: ?>
             <a href="#" onclick="return false;">Autres <em>Etape 4</em></a>
        <?php endif; ?>
    </li>
    <li class="<?php if ($etape > 5): ?>passe<?php elseif($etape == 5): ?>actif<?php endif; ?> <?php if($etapesConfig->isDisabled('stockage')): ?>disabled<?php endif; ?>">
        <?php if ($dr->exist('etape') && $etapesConfig->isAutorized($dr->etape, "stockage")): ?>
            <a href="<?php echo url_for('dr_stockage', $dr); ?>">Stockage <em>Etape 5</em></a>
        <?php else: ?>
             <a href="#" onclick="return false;">Stockage <em>Etape 5</em></a>
        <?php endif; ?>
    </li>
    <li class="<?php if ($etape > 6): ?>passe<?php elseif($etape == 6): ?>actif<?php endif; ?>">
        <?php if ($dr->exist('etape') && $etapesConfig->isAutorized($dr->etape, "validation")): ?>
            <a href="<?php echo url_for('dr_validation', $dr); ?>">Validation <em>Etape 6</em></a>
        <?php else: ?>
            <a href="#" onclick="return false;">Validation <em>Etape 6</em></a>
        <?php endif; ?>
    </li>
</ul>
