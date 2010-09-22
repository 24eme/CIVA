<div id="popup_rendements_max" class="popup_ajout" title="Rendements maximums autorisés">
    <p><strong>Exprimés <u>en vin clair</u></strong> c'est à dire après séparation des bourbes et des lies (décret du 05/11/02).</p><br />
    <table>
        <?php foreach ($rendement as $app=>$appellation) : ?>
        <tr>
            <td class="appellation"><?php echo $app; ?></td>
            <td  class="appellation">
                    <?php
                    $totalApp = count($appellation);
                    if($totalApp==1) {
                        foreach ($appellation as $rend=>$rd) {
                            if($rend == '-1') echo 'Pas de butoir';
                                else echo $rend.' hl/a';
                        }
                    }
                    ?>
            </td>
        </tr>
            <?php
            if($totalApp!=1) {
                foreach ($appellation as $rend=>$rd) {
                    $i=0;
                    $cepage = '- ';
                    foreach ($rd as $c=>$cep) {
                        if($i!=0) $cepage .=', ';
                        $cepage .= $c;
                        $i++;
                    }?>
                    <?php if($cepage == "- Rebêches") { ?>
        <tr>
            <td class="cepage" colspan="2">
                Les rebêche exclues du rendement autorisé doivent representer au minimum <?php echo $min_quantite; ?>% et au maximun 10% du volume total produit.
            </td>
        </tr>
                        <?php }else { ?>
        <tr>
            <td class="cepage"><?php echo $cepage; ?></td>
            <td class="rendement">
                                <?php
                                if($rend == '-1') echo 'Pas de butoir';
                                else echo $rend.' hl/a';
                                ?>
            </td>
        </tr>
                        <?php
                    }
                }
            }?>

        <tr><td class="vide">&nbsp;</td></tr>
        <?php endforeach; ?>
    </table>
    <div class="close_btn"><a class="close_popup" href=""><img alt="Fermer la fenetre" src="/images/boutons/btn_fermer.png"></a></div>
</div>