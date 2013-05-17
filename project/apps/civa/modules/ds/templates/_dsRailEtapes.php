<?php
use_helper('civaDs');
$dss = DSCivaClient::getInstance()->findDssByDS($ds);
$many_lieux = (count($dss) > 1);
$progression = progressionEdition($dss,$ds,$etape);

?>
<!-- .header_ds -->
<div class="header_ds clearfix">
        <ul id="etape_declaration" class="etapes_ds clearfix">
                <li <?php echo ($etape==1)? 'class="actif"' : '' ?> >
                        <a href="<?php echo url_for('ds');?>"><span>Exploitation</span> <em>Etape 1</em></a>
                </li>
                <li <?php echo ($etape==2)? 'class="actif"' : '' ?> >
                        <a href="<?php echo url_for("ds_lieux_stockage", $tiers); ?>"><span>Lieux de stockage</span> <em>Etape 2</em></a>
                </li>
                <li <?php echo ($etape==3)? 'class="actif"' : '' ?> >
                        <a href="#"><span>Stocks</span> <em>Etape 3 <?php echo getEtape3Label($many_lieux,$dss,$ds);?></em></a>
                </li>
                <li <?php echo ($etape==4)? 'class="actif"' : '' ?> >
                        <a href="#"><span>Autre</span> <em>Etape 4</em></a>
                </li>
                <li <?php echo ($etape==5)? 'class="actif"' : '' ?> >
                        <a href="#"><span>Validation</span> <em>Etape 5</em></a>
                </li>
        </ul>

        <div class="progression_ds">
                <p>Vous avez saisi <span><?php echo $progression;?>%</span> de votre DS</p>

                <div class="barre_progression">
                        <div class="progression" style="width: <?php echo $progression;?>%;"></div>
                </div>
        </div>
</div>
<!-- fin .header_ds -->