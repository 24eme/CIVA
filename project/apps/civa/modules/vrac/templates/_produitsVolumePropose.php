<?php use_helper('vrac') ?>
<?php use_helper('Float') ?>
<td class="volume <?php echo isVersionnerCssClass($detail, $quantiteType.'_propose') ?>">
    <span class="printonly"><?php echo ucfirst($quantiteType) ?> proposé<?php if ($quantiteType == 'surface') echo 'e'; ?> : </span><span id="prop<?php echo renderProduitIdentifiant($detail) ?>"><?php echoFloat($detail->get($quantiteType.'_propose')) ?></span>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl' ?>
    <?php if($detail->exist('dont_volume_bloque')): ?>
    <br /><small style="font-size: 94%; opacity: 0.7;">dont <?php echoFloat($detail->dont_volume_bloque); ?> hl en réserve</small>
    <?php endif; ?>
</td>
