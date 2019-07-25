<form id="" action="" method="post">
	<!-- .header_ds -->
	<div class="header_ds clearfix">
		
		<ul id="etape_declaration" class="etapes_ds clearfix">
			<li class="passe">
				<a href="#"><span>Exploitation</span> <em>Etape 1</em></a>
			</li>
			<li class="passe">
				<a href=""><span>Lieux de stockage</span> <em>Etape 2</em></a>
			</li>
			<li class="actif sous_menu">
				<a href="#"><span>Stocks</span> <em>Etape 3 (lieu 3/6)</em></a>
				<ul>
					<li><a href="#">Lieu de stockage n°1</a></li>
					<li><a href="#">Lieu de stockage n°2</a></li>
					<li class="actif"><a href="#">Lieu de stockage n°3</a></li>
					<li><a href="#">Lieu de stockage n°4</a></li>
					<li><a href="#">Lieu de stockage n°5</a></li>
					<li><a href="#">Lieu de stockage n°6</a></li>
				</ul>
			</li>
			<li>
				<a href="#"><span>Récapitulatif</span> <em>Etape 4</em></a>
			</li>
			<li>
				<a href="#"><span>Validation</span> <em>Etape 5</em></a>
			</li>
		</ul>
	
		<div class="progression_ds">
			<p>Vous avez saisi <span>60%</span> de votre DS</p>
	
			<div class="barre_progression">
				<div class="progression" style="width: 60%;"></div>
			</div>
		</div>
	</div>
	<!-- fin .header_ds -->
	
	<h2 class="titre_page">7523700100111 : Récapitulatif</h2>

	
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		
		<div id="recap_lieu_stockage">
			<div id="recap_appellations">
				<table class="table_donnees">
					<thead>
						<tr>
							<th class="appellation">Appellations</th>
							<th class="total">Total</th>
							<th>Stocks hors VT / SGN</th>
							<th>VT</th>
							<th>SGN</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>AOC Alsace</td>
							<td>100</td>
							<td>80</td>
							<td>10</td>
							<td>10</td>
						</tr>
						<tr>
							<td>AOC Alsace</td>
							<td>100</td>
							<td>80</td>
							<td>10</td>
							<td>10</td>
						</tr>
						<tr>
							<td>AOC Alsace</td>
							<td>100</td>
							<td>80</td>
							<td>10</td>
							<td>10</td>
						</tr>
						<tr>
							<td>AOC Alsace</td>
							<td>100</td>
							<td>80</td>
							<td>10</td>
							<td>10</td>
						</tr>
					</tbody>
				</table>
				
				<div id="total" class="ligne_total">
					<h3>Total AOC</h3>
					<input type="text" readonly="readonly" value="400" />
				</div>
			</div>
			
			<div id="blocs_autres">
				<div id="bloc_autres_sans_aoc" class="bloc_autres">
					<h2 class="titre_section">Sans AOC</h2>
					<div class="contenu_section">
						<ul class="bloc_vert">
							<li>
								<label>Vins de table - Vins sans IG</label>
								<input type="text" readonly="readonly" value="25" />
							</li>
							
							<li>
								<label>Vins de table - Mousseux</label>
								<input type="text" readonly="readonly" value="25" />
							</li>
						</ul>
					</div>
				</div>
				
				<div id="bloc_autres_autres" class="bloc_autres">
					<h2 class="titre_section">Autres</h2>
					<div class="contenu_section">
						<ul class="bloc_vert">
							<li>
								<label>Rébêches</label>
								<input type="text" readonly="readonly" value="25" />
							</li>
							
							<li>
								<label>DPLC Blanc</label>
								<input type="text" readonly="readonly" value="25" />
							</li>

							<li>
								<label>DPLC Rouge</label>
								<input type="text" readonly="readonly" value="25" />
							</li>
							
							<li>
								<label>Lies en Stocks</label>
								<input type="text" readonly="readonly" value="25" />
							</li>
							
							<li>
								<label>Moûts concentrés rectifiés</label>
								<input type="text" readonly="readonly" value="25" />
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<ul id="btn_appelation" class="btn_prev_suiv clearfix">
			<li>
				<a href="#">
					<img src="/images/boutons/btn_appelation_prec.png" alt="Retourner à l'étape précédente" />
				</a>
			</li>
			<li>
				<a href="#">
					<img src="/images/boutons/btn_lieu_stockage_suiv.png" alt="Valider et passer au lieu de stockage suivant" />
				</a>
			</li>
		</ul>
		
	</div>
	<!-- fin #application_ds -->
</form>

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="#">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
		<a href="#">
			<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante" />
		</a>
	</li>
</ul>





