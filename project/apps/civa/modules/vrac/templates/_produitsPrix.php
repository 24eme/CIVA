<td class="prix <?php echo isVersionnerCssClass($detail, 'prix_unitaire') ?>">
    <span class="printonly">Prix unitaire : </span><?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;<?php echo $vrac->getPrixUniteLibelle(); ?><?php endif; ?>
</td>
