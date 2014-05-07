<?php
use_helper('ds');
$dss = DSCivaClient::getInstance()->findDssByDS($ds);
$ds_principale = DSCivaClient::getInstance()->getDSPrincipaleByDs($ds);
$many_lieux = (count($dss) > 1);
$r = (isset($recap) && $recap==1);
$progression = progressionEdition($ds_principale->num_etape,$dss,$ds_principale,$r);
$ds_noAppellation = $ds_principale->hasNoAppellation();
$ds_neant = $ds_principale->isDsNeant();
?>
<!-- .header_ds -->
<div class="header_ds clearfix <?php if(($etape==3) && ($many_lieux)) echo "sous_menu"; ?>">
    <ul id="etape_declaration" class="etapes_ds clearfix">
            <?php 
            $passe = isset($force_passe) || isEtapePasse(1,  $ds_principale); 
            $to_linked = !isset($force_no_link) && ($passe || ($ds_principale->num_etape >=0));
            ?>
            <li class="<?php echo ($etape==1)? 'actif ' : ''; echo ($passe && $etape!=1)? 'passe' : ''; ?>" >
                <?php if($to_linked) : ?>
                        <a class="ajax" href="<?php echo  url_for('ds_exploitation',$ds_principale); ?>">
                <?php endif; ?>
                                <span>Exploitation</span> <em>Etape 1</em>
                <?php if($to_linked) echo "</a>"; ?>       
            </li>
            <?php 
            $passe = isset($force_passe) || isEtapePasse(2,  $ds_principale);
            $to_linked = !isset($force_no_link) && ($passe || ($etape>=1)); 
            ?>
            <li class="<?php echo ($etape==2)? 'actif ' : ''; echo ($passe && $etape!=2)? 'passe' : ''; ?>" >
                <?php if($to_linked) : ?> 
                        <a class="ajax" href="<?php echo url_for("ds_lieux_stockage", $ds_principale); ?>">
                <?php endif; ?>               
                                <span>Lieux de stockage</span> <em>Etape 2</em>
                <?php if($to_linked) echo "</a>"; ?>
            </li>
                <?php 
                $passe = isset($force_passe) || isEtapePasse(3,  $ds_principale);
                $to_linked = !isset($force_no_link) && ((!$ds_noAppellation && !$ds_neant) && ($passe || ($ds_principale->num_etape>=2))); 
                ?>
                <li class="<?php echo ((!$ds_noAppellation && !$ds_neant) && $etape==3)? 'actif ' : ''; echo ((!$ds_noAppellation && !$ds_neant) && $passe && $etape!=3)? 'passe ' : ''; ?> <?php echo (($etape==3) && ($many_lieux))? 'sous_menu' : '' ?>" >
                    <?php if($to_linked) : ?> 
                        <a class="ajax" href="<?php echo url_for('ds_edition_operateur', array('id' => DSCivaClient::getInstance()->getFirstDSByDs($ds_principale)->_id));?>">
                    <?php endif; ?> 
                        <span>Stocks</span> <em>Etape 3<span class="lieu" ><?php echo getEtape3Label($etape,$many_lieux,$dss,$ds);?></span></em>
                    <?php if($to_linked) echo "</a>"; ?>
                    <?php if(($etape==3) && ($many_lieux || $ds_principale->isAjoutLieuxDeStockage())) : ?>
                        <ul id="liste_lieux_stockage">
                            <?php 
                            $num = 1;
                            foreach ($dss as $current_ds) : ?>
                                <li class="<?php echo ($current_ds->_id == $ds->_id)? 'actif' : '' ?>">
                                    <a class="ajax" href="<?php echo url_for('ds_edition_operateur', array('id' => $current_ds->_id)); ?>">Lieu <?php echo ($current_ds->isDsPrincipale())? '' : 'de stockage'; ?> n°<?php echo $num; ?>  <?php echo ($current_ds->isDsPrincipale())? '(principal)' : ''; ?></a>
                                </li>
                            <?php 
                            $num++;
                            endforeach; 
                            if($ds_principale->isAjoutLieuxDeStockage()) :
                            ?>
                               <li class="ajouter_lieu_stockage">
                                    <a class="ajax" href="<?php echo url_for('ds_ajout_lieux_stockage', array('id' => $ds_principale->_id)); ?>"></a>
                               </li> 
                           <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </li>
                <?php 
                $passe = isset($force_passe) || isEtapePasse(4,  $ds_principale); 
                $to_linked = !isset($force_no_link) && ($passe || ($ds_principale->num_etape>=3)); 
                ?>                        
                <li class="<?php echo ($etape==4)? 'actif ' : ''; echo ($passe && $etape!=4)? 'passe' : ''; ?>" >
                <?php if($to_linked) : ?> 
                    <a class="ajax" href="<?php echo url_for('ds_autre', $ds_principale);?>">
                <?php endif; ?>         
                        <span>Autres Produits</span> <em>Etape 4</em>
                <?php if($to_linked) echo "</a>"; ?>
                </li>
                <?php 
                $passe = isset($force_passe) || isEtapePasse(5, $ds_principale);
                $to_linked = !isset($force_no_link) && ($passe || ($ds_principale->num_etape>=4)); 
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
                <p>Vous avez saisi <span><?php echo $progression.'%';?></span> de votre DS <a href="" class="msg_aide_ds" rel="help_popup_mon_espace_ds_general" title="Message aide"></a></p>
                <div class="barre_progression">
                        <div class="progression" style="<?php echo "width: ".$progression."%;";?>"></div>
                </div>
	</div>
</div>
<!-- fin .header_ds -->