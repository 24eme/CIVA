<div id="contrats_vrac" class="clearfix">
	
	<div class="annuaire">
		
		<div class="bloc_annuaire">
			<h2>Récoltants (<?php echo count($annuaire->recoltants) ?>)</h2>
			
			<div class="bloc">			
				<?php if (count($annuaire->recoltants) > 0): ?>
					<?php $i = 0; ?>
					<h3>Raison sociale</h3>
					<ul>
						<?php foreach ($annuaire->recoltants as $key => $item): ?>
							<?php $i++; ?>

							<?php if($i % 2 == 0): ?>
								<li class="alt"><?php echo $item ?> <small>(<?php echo $key ?>)</small><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'recoltants', 'id' => $key)) ?>" class="btn_supprimer">X</a></li>
							<?php else: ?>
								<li><?php echo $item ?> <small>(<?php echo $key ?>)</small><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'recoltants', 'id' => $key)) ?>" class="btn_supprimer">X</a></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>Aucun récoltants</p>
				<?php endif; ?>
			</div>

			<a href="#" class="btn"><img src="/images/boutons/btn_ajouter_recoltant.png" alt="Ajouter un récoltant" /></a>
		</div>
	
		<div class="bloc_annuaire">
			<h2>Négociants (<?php echo count($annuaire->negociants) ?>)</h2>
			<div class="bloc">
				<?php if (count($annuaire->negociants) > 0): ?>
					<?php $i = 0; ?>
					<h3>Raison sociale</h3>
					<ul>
						<?php foreach ($annuaire->negociants as $key => $item): ?>
							<?php $i++; ?>

							<?php if($i % 2 == 0): ?>
								<li class="alt"><?php echo $item ?> <small>(<?php echo $key ?>)</small><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'negociants', 'id' => $key)) ?>" class="btn_supprimer">X</a></li>
							<?php else: ?>
								<li><?php echo $item ?> <small>(<?php echo $key ?>)</small><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'negociants', 'id' => $key)) ?>" class="btn_supprimer">X</a></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>Aucun négociants</p>
				<?php endif; ?>
			</div>

			<a href="#" class="btn"><img src="/images/boutons/btn_ajouter_negociant.png" alt="Ajouter un négociant" /></a>
		</div>

		<div class="bloc_annuaire">
			<h2>Caves coopératives (<?php echo count($annuaire->caves_cooperatives) ?>)</h2>
			<div class="bloc">
				<?php if (count($annuaire->caves_cooperatives) > 0): ?>
					<?php $i = 0; ?>
					<h3>Raison sociale</h3>
					<ul>
						<?php foreach ($annuaire->caves_cooperatives as $key => $item): ?>
							<?php $i++; ?>

							<?php if($i % 2 == 0): ?>
								<li class="alt"><?php echo $item ?> <small>(<?php echo $key ?>)</small><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'caves_cooperatives', 'id' => $key)) ?>" class="btn_supprimer">X</a></li>
							<?php else: ?>
								<li><?php echo $item ?> <small>(<?php echo $key ?>)</small><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'caves_cooperatives', 'id' => $key)) ?>" class="btn_supprimer">X</a></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>Aucun négociants</p>
				<?php endif; ?>
			</div>

			<a href="<?php echo url_for('@annuaire_selectionner') ?>" class="btn"><img src="/images/boutons/btn_ajouter_cave.png" alt="Ajouter une cave coopérative" /></a>
		</div>

		<div class="bloc_annuaire">
			<h2>Interlocuteurs commerciaux (<?php echo count($annuaire->commerciaux) ?>)</h2>
			<div class="bloc">
				<?php if (count($annuaire->commerciaux) > 0): ?>
					<?php $i = 0; ?>
					<h3>Raison sociale</h3>
					<ul>
						<?php foreach ($annuaire->commerciaux as $key => $item): ?>
							<?php $i++; ?>

							<?php if($i % 2 == 0): ?>
								<li class="alt"><?php echo $item ?> <a href="<?php echo url_for('annuaire_supprimer', array('type' => 'commerciaux', 'id' => $key)) ?>" class="btn_supprimer">X</a></li>
							<?php else: ?>
								<li><?php echo $item ?> <a href="<?php echo url_for('annuaire_supprimer', array('type' => 'commerciaux', 'id' => $key)) ?>" class="btn_supprimer">X</a></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php else: ?>
					<p>Aucun commercial</p>
				<?php endif; ?>
			</div>

			<a href="<?php echo url_for('@annuaire_commercial_ajouter') ?>" class="btn"><img src="/images/boutons/btn_ajout_commercial.png" alt="Ajouter un interlocuteur commercial" /></a>
		</div>
	</div>
</div>

<ul id="btn_etape" class="btn_prev_suiv">
	<li><a href="#"><img src="/images/boutons/btn_retour_espace_contrats.png" alt="Retourner à l'espace contrats" /></a></li>
</ul>

