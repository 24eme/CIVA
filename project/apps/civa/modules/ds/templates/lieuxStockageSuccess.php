<!-- .header_ds -->
<form action="<?php echo url_for( 'ds_lieux_stockage', $tiers); ?>" method="POST">
<?php
    echo $form->renderHiddenFields();
    echo $form->renderGlobalErrors();
    $dss[0]->getFirstAppellationLieu();
?>  
<div class="header_ds clearfix">
	
	<ul id="etape_declaration" class="etapes_ds clearfix">
		<li>
			<a href="#"><span>Exploitation</span> <em>Etape 1</em></a>
		</li>
		<li class="actif">
			<a href="#"><span>Lieux de stockage</span> <em>Etape 2</em></a>
		</li>
		<li>
			<a href="<?php echo count($dss)? url_for("ds_edition_operateur", array('id' => $dss[0]->_id,'appellation_lieu' => $dss[0]->getFirstAppellationLieu())) : '#' ?>"><span>Stocks</span> <em>Etape 3</em></a>
		</li>
		<li>
			<a href="#"><span>Validation</span> <em>Etape 5</em></a>
		</li>	
	</ul>

	<div class="progression_ds">
		<p>Vous avez saisi <span>20%</span> de votre DS</p>

		<div class="barre_progression">
			<div class="progression"></div>
		</div>
	</div>
</div>
<!-- fin .header_ds -->

<p id="adresse_stock">Déclarations de Stocks de Vins d'Alsace au 31 juillet 2013</p>

<a href="#" id="def_lieux_stockage">Définition des lieux de stockage</a>

<!-- #application_ds -->
<div id="application_ds" class="clearfix">
	
	<table id="lieux_stockage">
		<thead>
			<tr>
				<th>Lieu de stockage</th>
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
			<?php foreach($tiers->add('lieux_stockage') as $numero => $lieu_stockage): ?>
			<tr>
				<td class="adresse_lieu">
					<?php echo $lieu_stockage->numero ?> <br />
					<?php echo $lieu_stockage->adresse ?> <?php echo $lieu_stockage->code_postal ?> <?php echo $lieu_stockage->commune ?>
				</td>
                                <?php  $cpt = 0;
                                 $name = 'lieuxStockage_'.str_replace($tiers->cvi, '', $numero);
                                    foreach ($form->getWidget($name)->getChoices() as $key => $value): 
                                        $paire = ($cpt%2==0)? 'paire' : '';
                                        $checked = (in_array($key, $form[$name]->getValue()))? 'checked="checked"' : '';
                                    ?>
                                
                                <td class="<?php echo $paire ?>">
                                
                                <input type="checkbox" name="<?php echo $form[$name]->renderName().'[]'; ?>" id="<?php echo $form[$name]->renderId() . "_" . str_replace('/','_',$key); ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> >
                                </td>
                               <?php $cpt++;
                               endforeach; ?>				
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	
</div>
<!-- fin #application_ds -->


<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="<?php echo url_for("ds") ?>">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
            <button type="submit">Valider</button>
            <img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante"/>
	</li>
</ul>
</form>



