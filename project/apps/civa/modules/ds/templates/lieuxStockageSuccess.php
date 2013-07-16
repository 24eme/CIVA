<?php use_helper('ds'); ?>
<?php 
$dss = $dss->getRawValue();
$hasVolume = false;
?>
<form action="<?php echo url_for('ds_lieux_stockage', $ds); ?>" method="POST" id="form_lieux_stockage_<?php echo $tiers->cvi; ?>" class="ajaxForm">
<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 2)); ?>
<?php
    echo $form->renderHiddenFields();
    echo $form->renderGlobalErrors();
?>  

<ul id="onglets_majeurs" class="clearfix">
	<li class="ui-tabs-selected"><a href="#">Lieux de stockage</a></li>
        <a href="" class="msg_aide_ds" rel="help_popup_ds_lieux_stockage" title="Message aide"></a>
</ul>
    

<!-- #application_ds -->
<div id="application_ds" class="clearfix">
	
	<p class="intro_declaration">Définissez ici le détail de vos lieux de stockage</p>
	
	<div class="ds_neant">
		<?php echo $form['neant']->renderLabel(); ?>
		<input type="checkbox" name="<?php echo $form['neant']->renderName().'[]'; ?>" id="<?php echo $form['neant']->renderId(); ?>" value="<?php echo "1"; ?>" <?php echo ($ds->isDsNeant())? "checked='checked'" : '' ?>  <?php echo (!$ds->hasNoAppellation() &&  !isset($error))? "readonly='readonly'" : ''; ?> />
                <a href="" class="msg_aide_ds" rel="help_popup_ds_lieux_stockage_neant" title="Message aide"></a>
        </div>
	
	<div id="lieux_stockage">
		<table class="table_donnees">
			<thead>
				<tr>
					<th>Lieux de stockage</th>
					<?php 
					$configurations = ConfigurationClient::getConfiguration()->getArrayAppellations();
					foreach ($configurations as $conf):
					?>
					
					<th><?php $l = $conf->getLibelle();
						echo (($aoc = substr($l,0,3))=='AOC')? $aoc : ''; ?>
						<span><?php echo (substr($l,0,3)=='AOC')? substr($l,4) : $l; ?></span></th>
					<?php
					endforeach;
					?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($tiers->add('lieux_stockage') as $numero => $lieu_stockage): 
                                                $num_lieu = str_replace($tiers->cvi, '', $numero);
                                                $ds_id = preg_replace("/[0-9]{3}$/", $num_lieu, $ds->_id);
                                    ?>
				<tr>
					<td class="adresse_lieu">
                                                <?php echo ($num_lieu == $ds->getLieuStockage())? "<strong>" : ""; ?>
                                                
						<?php echo formatNumeroStockage($lieu_stockage->numero) ?> <br />
						<?php echo $lieu_stockage->adresse ?> <?php echo $lieu_stockage->code_postal ?> <?php echo $lieu_stockage->commune ?>
					<?php echo ($num_lieu == $ds->getLieuStockage())? "</strong>" : ""; ?>
                                        </td>
					<?php  $cpt = 0;
					 $name = 'lieuxStockage_'.$num_lieu;
						foreach ($form->getWidget($name)->getChoices() as $key => $value): 
							$paire = ($cpt%2==0)? 'paire' : '';
							$checked = ($form[$name]->getValue() && in_array($key, $form[$name]->getValue()))? 'checked="checked"' : '';
                                                        $current_ds = (array_key_exists($ds_id, $dss))? $dss[$ds_id] : null;
                                                        $disabled = ($current_ds && $current_ds->exist($key) && $current_ds->get($key)->hasVolume());
                                                        if($disabled){
                                                            $hasVolume =true;  
                                                        }
						?>
					
					<td class="<?php echo $paire ?>">
					<?php if($disabled): ?>
					<input type="hidden" name="<?php echo $form[$name]->renderName().'[]'; ?>" id="<?php echo $form[$name]->renderId() . "_" . str_replace('/','_',$key); ?>" value="<?php echo $key; ?>">
					<input type="checkbox" name="checkbox_disabled[]'; ?>" id="<?php echo $form[$name]->renderId() . "_" . str_replace('/','_',$key); ?>_disabled" value="<?php echo $key; ?>" <?php echo $checked; ?> <?php echo ($disabled) ? 'disabled="disabled"' : '' ?> />
					<?php else: ?>
					<input type="checkbox" name="<?php echo $form[$name]->renderName().'[]'; ?>" id="<?php echo $form[$name]->renderId() . "_" . str_replace('/','_',$key); ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> <?php echo ($ds->isDsNeant()) ? 'disabled="disabled"' : '' ?> />
					</td>
					<?php endif; ?>
				   <?php $cpt++;
				   endforeach; ?>				
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>	
</div>

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
            <a href="<?php echo url_for("ds_exploitation", $ds) ?>" class="ajax">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
	    </a>
	</li>
	<li class="suiv">
		 <input type="image" src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante"/>
	</li>
</ul>
</form>
<?php include_partial('popupLieuxStockageNeant', array('hasVolume' => $hasVolume)); ?>



