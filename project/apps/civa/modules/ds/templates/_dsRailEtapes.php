<?php
use_helper('ds');
$dss = DSCivaClient::getInstance()->findDssByDS($ds);
$ds_principale = DSCivaClient::getInstance()->getDSPrincipaleByDs($ds);
$many_lieux = (count($dss) > 1);
$progression = progressionEdition($dss,$ds,$ds_principale->num_etape);
$ds_noAppellation = $ds_principale->hasNoAppellation();
$ds_neant = $ds_principale->isDsNeant();
?>
<!-- .header_ds -->
<div class="header_ds clearfix <?php if(($etape==3) && ($many_lieux)) echo "sous_menu"; ?>">
    <ul id="etape_declaration" class="etapes_ds clearfix">
                        <?php 
                        $passe = isset($force_passe) || isEtapePasse(1, $dss, $ds_principale); 
                        $to_linked = !isset($force_no_link) && ($passe || ($etape>=1));
                        ?>
			<li class="<?php echo ($etape==1)? 'actif ' : ''; echo ($passe && $etape!=1)? 'passe' : ''; ?>" >
                            <?php if($to_linked) : ?>
					<a class="ajax" href="<?php echo  url_for('ds_exploitation',$ds_principale); ?>">
                            <?php endif; ?>
                                            <span>Exploitation</span> <em>Etape 1</em>
                            <?php if($to_linked) echo "</a>"; ?>       
			</li>
                        <?php 
                        $passe = isset($force_passe) || isEtapePasse(2, $dss, $ds_principale);
                        $to_linked = !isset($force_no_link) && ($passe || ($etape>=2)); 
                        ?>
			<li class="<?php echo ($etape==2)? 'actif ' : ''; echo ($passe && $etape!=2)? 'passe' : ''; ?>" >
                            <?php if($to_linked) : ?> 
					<a class="ajax" href="<?php echo url_for("ds_lieux_stockage", $ds_principale); ?>">
                            <?php endif; ?>               
                                            <span>Lieux de stockage</span> <em>Etape 2</em>
                            <?php if($to_linked) echo "</a>"; ?>
			</li>
                        <?php 
                        $passe = isset($force_passe) || isEtapePasse(3, $dss, $ds_principale);
                        $to_linked = !isset($force_no_link) && ((!$ds_noAppellation && !$ds_neant) && ($passe || ($etape>=3))); 
                        ?>
			<li class="<?php echo ((!$ds_noAppellation && !$ds_neant) && $etape==3)? 'actif ' : ''; echo ((!$ds_noAppellation && !$ds_neant) && $passe && $etape!=3)? 'passe ' : ''; ?> <?php echo (($etape==3) && ($many_lieux))? 'sous_menu' : '' ?>" >
                            <?php if($to_linked) : ?> 
                                <a class="ajax" href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id));?>">
                            <?php endif; ?> 
                                <span>Stocks</span> <em>Etape 3<span class="lieu" ><?php echo getEtape3Label($etape,$many_lieux,$dss,$ds);?></span></em>
                            <?php if($to_linked) echo "</a>"; ?>
                            <?php if(($etape==3) && ($many_lieux)) : ?>
                                <ul id="liste_lieux_stockage">
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
                        <?php 
                        $passe = isset($force_passe) || isEtapePasse(4, $dss, $ds_principale); 
                        $to_linked = !isset($force_no_link) && (((!$ds_neant) && ($passe || ($etape>=4)))); 
                        ?>                        
			<li class="<?php echo ((!$ds_neant) && $etape==4)? 'actif ' : ''; echo ((!$ds_neant) && $passe && $etape!=4)? 'passe' : ''; ?>" >
                        <?php if($to_linked) : ?> 
                            <a class="ajax" href="<?php echo url_for('ds_autre', $ds_principale);?>">
                        <?php endif; ?>         
                                <span>Autres Produits</span> <em>Etape 4</em>
                        <?php if($to_linked) echo "</a>"; ?>
			</li>
                        <?php 
                        $passe = isset($force_passe) || isEtapePasse(5, $dss, $ds_principale);
                        $to_linked = !isset($force_no_link) && ($passe || ($etape>=5)); 
                        ?>   
			<li class="<?php echo ($etape==5)? 'actif ' : ''; echo ($passe && $etape!=5)? 'passe' : ''; ?>" >
                        <?php if($to_linked) : ?> 
                            <a class="ajax" href="<?php echo url_for('ds_validation', $ds_principale);?>">
                       <?php endif; ?>  
                                <span>Validation</span> <em>Etape 5</em>
                         <?php if($to_linked) echo "</a>"; ?>
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