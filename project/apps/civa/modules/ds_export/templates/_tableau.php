<?php use_helper('Float') ?>
<?php use_helper('dsExport') ?>
<?php $empty = isset($empty) && $empty; ?>
<?php $is_first_page = isset($is_first_page) && $is_first_page; ?>
<?php $colWidth = round(318 / count($tableau['colonnes'])); ?>
<?php $tableau = $tableau->getRawValue(); ?>
<?php $isTotalAOC = (isset($tableau['totalAOC']) && $tableau['totalAOC']); ?>

<table border="1" cellspacing=0 cellpadding=0 style="text-align: right; border: 1px solid black;">
    <?php if (!array_key_exists('no_header', $tableau) || !$tableau['no_header']): ?>
        <tr>
            <?php foreach ($tableau['colonnes'] as $libelle): ?>
                <th style="text-align: left; font-weight: bold; width: <?php echo $colWidth ?>px; border: 1px solid black; <?php if($isTotalAOC): ?>background-color: black; color: white; text-align: center;<?php endif; ?>">&nbsp;<?php echo $libelle ?></th>
            <?php endforeach; ?>
            <th style="font-weight: bold; width: 106px; text-align: center;  border: 1px solid black; <?php if($isTotalAOC): ?>background-color: black; color: white;<?php endif; ?>">hors VT et SGN</th>
            <th style="font-weight: bold; width: 106px; text-align: center;  border: 1px solid black; <?php if($isTotalAOC): ?>background-color: black; color: white;<?php endif; ?>">VT</th>
            <th style="font-weight: bold; width: 106px; text-align: center;  border: 1px solid black; <?php if($isTotalAOC): ?>background-color: black; color: white;<?php endif; ?>">SGN</th>
        </tr>
    <?php endif; ?>
    <?php
    $produitEmpty = (count($tableau['produits']) == 1 && key($tableau['produits']) == "empty");
    $produitFixed = (array_key_exists('fixed', $tableau) && $tableau['fixed']);
    $nbColonne = 0;
    foreach ($tableau['produits'] as $key => $produit):

        $last_produit = ($empty && array_key_exists('last', $produit) && $produit['last']);
        $nbColonne = count($produit['colonnes']);
        $vtsgn = true;
        if ($empty && array_key_exists('vtsgn', $produit)):
            $vtsgn = $produit['vtsgn'];
        endif;
        ?>
        <tr>
            <?php foreach ($produit['colonnes'] as $colonne): ?>
                <?php if ($colonne["rowspan"] > 0): ?>
                    <td style="text-align: left; border: 1px solid black; width: <?php echo $colWidth ?>px; <?php if (is_null($colonne['libelle'])): ?>background-color: #bbb;<?php endif; ?> <?php if($isTotalAOC): ?>text-align: center<?php endif; ?>" rowspan="<?php echo $colonne["rowspan"] ?>">&nbsp;<?php if($isTotalAOC): ?><?php echoVolume($colonne['libelle']); ?><?php else: ?><?php echo truncate_text($colonne['libelle'], round(50 / count($tableau['colonnes']))) ?><?php endif; ?></td>
                <?php endif; ?>
            <?php endforeach; ?>
            <td style="border: 1px solid black; width: 106px; <?php if ((!$empty && is_null($produit["normal"])) || $produit["normal"] === false): ?>background-color: #bbb;<?php endif; ?>">
                <?php echoVolume($produit["normal"]); ?>
                
                <?php if (is_null($produit["normal"]) || $produitFixed): ?>
                    <?php echoHl($empty); ?>
                <?php endif; ?>
            </td>
            <?php if(array_key_exists('vt', $produit)): ?>
            <td style="border: 1px solid black; width: 106px;<?php if (($empty && !$vtsgn) || (is_null($produit["vt"]) && !$produitEmpty)): ?>background-color: #bbb;<?php endif; ?>">
                <?php echoVolume($produit["vt"]); ?>
                <?php  if (((is_null($produit["vt"]) && $produitEmpty) || $produitFixed) && $vtsgn): ?>
                    <?php  echoHl($empty);  ?>
                <?php  endif; ?>
            </td>
            <?php endif; ?>
            <?php if(array_key_exists('sgn', $produit)): ?>
            <td style="border: 1px solid black; width: 106px; <?php if (($empty && !$vtsgn) || (is_null($produit["sgn"]) && !$produitEmpty)): ?>background-color: #bbb;<?php endif; ?>">
                <?php echoVolume($produit["sgn"]); ?>
                <?php  if (((is_null($produit["sgn"]) && $produitEmpty) || $produitFixed) && $vtsgn): ?>
                    <?php  echoHl($empty); ?>
                <?php  endif; ?>
            </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    <?php if ($empty && !$produitFixed && $last_produit): ?>  
        <?php for ($index = (int) $produitEmpty; $index < 3; $index++): ?>
            <tr>
                <?php foreach ($produit['colonnes'] as $colonne): ?>
                    <td style="text-align: left; border: 1px solid black; width: <?php echo $colWidth ?>px;" rowspan="<?php echo $colonne["rowspan"] ?>">&nbsp;</td>
                <?php endforeach; ?>
                <td style="border: 1px solid black; width: 106px;">&nbsp;<?php echoHl($empty); ?></td>
                <td style="border: 1px solid black; width: 106px;">&nbsp;<?php echoHl($empty); ?></td>
                <td style="border: 1px solid black; width: 106px;">&nbsp;<?php echoHl($empty); ?></td>
            </tr>

        <?php endfor; ?>
    <?php endif ?>

    <?php if (!$empty): ?>
        <tr>
            <td style="text-align: left; border: 1px solid black;" colspan="<?php echo count($tableau['colonnes']) ?>">&nbsp;<b>Total</b><?php if ($tableau['total_suivante']): ?>&nbsp;&nbsp;<small>(page suivante)</small>&nbsp;<?php endif; ?>
            </td>    
            <?php if (!isset($tableau['total']) || $tableau['total_suivante']): ?>
                <td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>
                <td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>
                <td style="border: 1px solid black; background-color: #bbb;">&nbsp;</td>
            <?php elseif (isset($tableau['total']) && !$tableau['total_suivante'] && is_null($tableau["total"]["normal"]) && is_null($tableau["total"]["vt"]) && is_null($tableau["total"]["sgn"])): ?>
                <td colspan="3" style="border: 1px solid black; text-align:center;"><i>Néant</i></td>
            <?php else: ?>
                <td style="border: 1px solid black; <?php if (is_null($tableau["total"]["normal"])): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($tableau["total"]["normal"], true) ?></td>
                <td style="border: 1px solid black; <?php if (is_null($tableau["total"]["vt"])): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($tableau["total"]["vt"], true) ?></td>
                <td style="border: 1px solid black; <?php if (is_null($tableau["total"]["sgn"])): ?>background-color: #bbb;<?php endif; ?>"><?php echoVolume($tableau["total"]["sgn"], true) ?></td>
            <?php endif; ?>
        </tr>
    <?php elseif($tableau['total_suivante']): ?>
        <tr>
            <td colspan="2" style="text-align: left; border: 1px solid black;" colspan="<?php echo count($tableau['colonnes']) ?>">&nbsp; <small>(page suivante)</small>&nbsp;
            </td>
        </tr>
    <?php endif; ?>

</table>
<small><br /></small>