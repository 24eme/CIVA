
    <p><strong>Exprimés <u>en vin clair</u></strong> c'est à dire après séparation des bourbes et des lies (décret du 05/11/02).</p><br />
    <table>
        <?php foreach ($rendement as $app=>$appellation) : ?>
        <tr>
            <td class="appellation"><?php echo $app; ?></td>
            <td  class="appellation">
                    <?php
                        if (isset($appellation['appellation']))
                        foreach ($appellation['appellation'] as $rend=>$rd) {
                            if($rend == '-1')
                            	echo 'Pas de butoir';
                            else {
                            	if ($rend)
                            		echo $rend.' hl/ha';
                            }
                        }
                    
                    ?>
            </td>
        </tr>
            <?php
                if (isset($appellation['cepage']))
                foreach ($appellation['cepage'] as $rend=>$rd) {
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
                Les rebêches exclues du rendement autorisé doivent représenter au minimum <?php echo $min_quantite; ?>% et au maximun <?php echo $max_quantite; ?>% du volume total produit.
            </td>
        </tr>
                        <?php }else { ?>
        <tr>
            <td class="cepage"><?php echo $cepage; ?></td>
            <td class="rendement">
                                <?php
                                if($rend == '-1') echo 'Pas de butoir';
                                else echo $rend.' hl/ha';
                                ?>
            </td>
        </tr>
                        <?php
                    }
                }
            ?>

        <tr><td class="vide">&nbsp;</td></tr>
        <?php endforeach; ?>
    </table>
    <p style="color: white;"><?php echo $configuration_id; ?></p>
