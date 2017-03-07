<?php use_helper('ds'); ?>
<?php
$dss = $dss->getRawValue();
$hasVolume = false;
?>
<form action="<?php echo url_for('ds_lieux_stockage', $ds); ?>" method="POST" id="form_lieux_stockage_<?php echo $tiers->getIdentifiant(); ?>" class="ajaxForm">
<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 2)); ?>
<?php
    echo $form->renderHiddenFields();
    echo $form->renderGlobalErrors();
?>

<ul id="onglets_majeurs" class="clearfix">
	<li class="ui-tabs-selected"><a href="#">Lieux de stockage</a></li>
            <a href="" class="msg_aide_ds" rel="help_popup_ds_lieux_stockage<?php if($ds->type_ds == DSCivaClient::TYPE_DS_NEGOCE): ?>_negoce<?php endif; ?>" title="Message aide"></a>

        	<?php if($ds->isDateDepotMairie()):  ?>
                &nbsp; &nbsp;
                <?php echo $form['date_depot_mairie']->renderError(); ?>
                    <?php echo $form['date_depot_mairie']->renderLabel(); ?>
                <?php echo $form['date_depot_mairie']->render(array('class' => "datepicker")); ?>

        <?php endif; ?>
</ul>



<!-- #application_ds -->
<div id="application_ds" class="clearfix">
	<p class="intro_declaration">Définissez ici le détail de vos lieux de stockage</p>
    <div class="ds_neant">
		<?php echo $form['neant']->renderLabel(); ?>
		<input type="checkbox" name="<?php echo $form['neant']->renderName().'[]'; ?>" id="<?php echo $form['neant']->renderId(); ?>" value="<?php echo "1"; ?>" <?php echo ($ds->isDsNeant())? "checked='checked'" : '' ?>  <?php echo (!$ds->hasNoAppellation() &&  !isset($error))? "readonly='readonly'" : ''; ?> />
                <a href="" class="msg_aide_ds" rel="help_popup_ds_lieux_stockage_neant" title="Message aide"></a>
        </div>
        <div class="ds_lieu_toggle_stockage_principale">
            <?php if(count($tiers->getLieuxStockage(true, $ds->getIdentifiant())) > 1) : ?>
                <a href="#" class="" id="ds_lieux_stockage_toggle">Changer lieu de stockage principal</a>
            <?php endif; ?>
    </div>
	<div id="lieux_stockage">
		<table class="table_donnees pyjama_auto">
			<thead>
				<tr>
                                    <th colspan="2">Lieux de stockage</th>
					<?php
					$configurations = DSCivaClient::getInstance()->getConfigAppellations($ds->getConfig());
					foreach ($configurations as $conf):
					?>
					<th><?php
                                                $l = $conf->getLibelle();
						echo (($aoc = substr($l,0,3))=='AOC')? $aoc : '';
                                             ?>
                                        <span><?php echo (substr($l,0,3)=='AOC')? substr($l,4) : $l; ?></span></th>
					<?php
					endforeach;
					?>

				</tr>
			</thead>
			<tbody>
				<?php foreach($tiers->getLieuxStockage(true, $ds->getIdentifiant()) as $numero => $lieu_stockage):
                                                $identifiant = $ds->getIdentifiant();
                                                $num_lieu = str_replace($identifiant, '', $numero);
                                                $ds_id = preg_replace("/[0-9]{3}$/", $num_lieu, $ds->_id);
                                                $current_ds = (array_key_exists($ds_id, $dss))? $dss[$ds_id] : null;
                                    ?>
				<tr>
                                        <td>
                                        <input style='visibility:hidden;' type="radio" name="<?php echo $form['ds_principale']->renderName(); ?>" id="<?php echo $form['ds_principale']->renderId() . "_" . $num_lieu; ?>" value="<?php echo $num_lieu; ?>" <?php echo ($current_ds && $current_ds->isDsPrincipale()) ? 'checked="checked"' : '' ?> />
					</td>
                                        <td class="adresse_lieu <?php echo ($num_lieu == $ds->getLieuStockage())? "ds_lieu_principal_bold" : ""; ?>" id="<?php echo "adresse_".$num_lieu; ?>">
                                                <?php echo formatNumeroStockage($lieu_stockage->numero, $ds->isAjoutLieuxDeStockage()) ?>
                                                <?php echo ($num_lieu == $ds->getLieuStockage())? "<span id='principal_label'>(principal)</span>" : ""; ?>
                                                <br />
						<?php echo $lieu_stockage->adresse ?> <?php echo $lieu_stockage->code_postal ?> <?php echo $lieu_stockage->commune ?>
                                        </td>
					<?php  $cpt = 0;
					 $name = 'lieuxStockage_'.$num_lieu;
						foreach ($form->getWidget($name)->getChoices() as $key => $value):
							$paire = ($cpt%2==0)? 'paire' : '';
							$checked = ($form[$name]->getValue() && in_array($key, $form[$name]->getValue()))? 'checked="checked"' : '';
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
        <?php if($ds->isTypeDsNegoce()): ?>
            <a class="btn_majeur btn_petit btn_vert" style="float: left; margin-top: 20px;" href="<?php echo url_for('ds_ajout_lieux_stockage', array('id' => $ds->_id)); ?>">Ajout d'un lieu de stockage</a>
       <?php endif; ?>
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
