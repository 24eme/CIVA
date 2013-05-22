<?php
use_helper('ds');
$dss = DSCivaClient::getInstance()->findDssByDS($ds);
$many_lieux = (count($dss) > 1);
$progression = progressionEdition($dss,$ds,$etape);
?>
<!-- .header_ds -->
<div class="header_ds clearfix">
    <ul id="etape_declaration" class="etapes_ds clearfix">
			<li <?php echo ($etape==1)? 'class="actif"' : '' ?> >
					<a class="ajax" href="<?php echo url_for('ds');?>"><span>Exploitation</span> <em>Etape 1</em></a>
			</li>
			<li <?php echo ($etape==2)? 'class="actif"' : '' ?> >
					<a class="ajax" href="<?php echo url_for("ds_lieux_stockage", $tiers); ?>"><span>Lieux de stockage</span> <em>Etape 2</em></a>
			</li>
			<li class="<?php echo ($etape==3)? 'actif' : '' ?> <?php echo (($etape==3) && ($many_lieux))? 'sous_menu' : '' ?>" >
                            <a class="ajax" href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id));?>"><span>Stocks</span> <em>Etape 3<span class="lieu" ><?php echo getEtape3Label($etape,$many_lieux,$dss,$ds);?></span></em></a>
                            <?php if(($etape==3) && ($many_lieux)) : ?>
                                <ul>
                                <?php 
                                $num = 1;
                                foreach ($dss as $current_ds) : ?>
                                    <li class="<?php echo ($current_ds->_id == $ds->_id)? 'actif' : '' ?>">
                                            <a href="<?php echo url_for('ds_edition_operateur', array('id' => $current_ds->_id)); ?>">Lieu de stockage n°<?php echo $num; ?></a>
                                    </li>
                                <?php 
                                $num++;
                                endforeach; ?>
                                </ul>
                            <?php endif; ?>
			</li>
			<li <?php echo ($etape==4)? 'class="actif"' : '' ?> >
					<a class="ajax" href="<?php echo url_for('ds_autre', $tiers);?>"><span>Autre</span> <em>Etape 4</em></a>
			</li>
			<li <?php echo ($etape==5)? 'class="actif"' : '' ?> >
					<a class="ajax" href="<?php echo url_for('ds_validation', $tiers);?>"><span>Validation</span> <em>Etape 5</em></a>
			</li>
	</ul>
		
	<div class="progression_ds">
			<p>Vous avez saisi <span><?php echo $progression.'%';?></span> de votre DS</p>

			<div class="barre_progression">
					<div class="progression" style="<?php echo "width: ".$progression."%;";?>"></div>
			</div>
	</div>
</div>
<!-- fin .header_ds -->