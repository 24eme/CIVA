<?php use_helper('vrac') ?>
<td class="volume <?php echo isVersionnerCssClass($detail, $quantiteType.'_propose') ?>">
    <span class="printonly"><?php echo ucfirst($quantiteType) ?> proposé<?php if ($quantiteType == 'surface') echo 'e'; ?> : </span><?php echoFloat($detail->get($quantiteType.'_propose')) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ares' : 'hl' ?>
</td>
