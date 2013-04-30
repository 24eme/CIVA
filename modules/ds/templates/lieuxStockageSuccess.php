<!-- .header_ds -->
<div class="header_ds clearfix">
	
	<ul id="etape_declaration" class="etapes_ds clearfix">
		<li class="passe">
			<a href="#"><span>Exploitation</span> <em>Etape 1</em></a>
		</li>
		<li class="actif">
			<a href="#"><span>Lieux de stockage</span> <em>Etape 2</em></a>
		</li>
		<li>
			<a href="<?php echo url_for("ds_edition_operateur", $ds) ?>"><span>Stocks</span> <em>Etape 3</em></a>
		</li>
		<li>
			<a href="#"><span>Récapitulatif</span> <em>Etape 4</em></a>
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

<h2 class="titre_page">Déclarations de Stocks de Vins d'Alsace au 31 juillet 2013</h2>

<ul id="onglets_majeurs" class="clearfix">
	<li class="ui-tabs-selected">
		<a href="#">Définition des lieux de stockage</a>
	</li>
</ul>

<!-- #application_ds -->
<div id="application_ds" class="clearfix">
	
	<div id="lieux_stockage">
		<table class="table_donnees">
			<thead>
				<tr>
					<th>Lieu de stockage</th>
					<th><span>AOC</span> Alsace blanc</th>
					<th><span>AOC</span> Alsace Lieu-dit</th>
					<th><span>AOC</span> Alsace Communale</th>
					<th><span>AOC</span> Grands Crus</th>
					<th><span>AOC</span> Alsace Pinot noir</th>
					<th><span>AOC</span> Alsace PN rouge</th>
					<th><span>AOC</span> Crémant d'Alsace</th>
					<th>Vins sans IG</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($tiers->add('lieux_stockage') as $numero => $lieu_stockage): ?>
				<tr>
					<td class="adresse_lieu">
						<?php echo $lieu_stockage->numero ?> <br />
						<?php echo $lieu_stockage->adresse ?> <?php echo $lieu_stockage->code_postal ?> <?php echo $lieu_stockage->commune ?>
					</td>
					<td><input type="checkbox" /></td>
					<td><input type="checkbox" /></td>
					<td><input type="checkbox" /></td>
					<td><input type="checkbox" /></td>
					<td><input type="checkbox" /></td>
					<td><input type="checkbox" /></td>
					<td><input type="checkbox" /></td>
					<td><input type="checkbox" /></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<!-- fin #application_ds -->


<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="#">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
		<a href="<?php echo url_for("ds_edition_operateur", $ds) ?>">
			<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>



