<?php use_helper('ds') ?>
<?php if($ds->isTypeDsNegoce()): ?>
Stocks Coopération et Négoce
<?php endif;?>
<?php $ds->declarant->nom ?>
<?php if($ds->isTypeDsPropriete()): ?>
Commune de déclaration : <?php echo $ds->declarant->nom ?>
<?php endif;?>
<?php echo getTitleLieuStockageStock($ds); ?>