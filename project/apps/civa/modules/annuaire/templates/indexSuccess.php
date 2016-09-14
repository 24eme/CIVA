<div id="contrats_vrac" class="clearfix">
	<h2 class="titre_principal">Annuaire de vos contacts</h2>

	<div class="fond">
		<div class="annuaire clearfix">
			<div class="bloc_annuaire">
				<h2 class="titre_section">Récoltants (<?php echo count($annuaire->recoltants) ?>)</h2>

				<div class="bloc">
					<?php if (count($annuaire->recoltants) > 0): ?>
						<?php $i = 0; ?>
						<ul>
							<?php foreach ($annuaire->getAnnuaireSorted('recoltants') as $key => $item): ?>
								<?php if ($i % 2 == 0): ?>
									<li><?php echo $item ?> <span class="infos">(<?php echo $key; ?>)</span><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'recoltants', 'id' => $key)) ?>" onclick="return confirm('Confirmez-vous la suppression du récoltant ?')" class="btn_supprimer">X</a></li>
								<?php else: ?>
									<li class="alt"><?php echo $item ?> <span class="infos">(<?php echo $key; ?>)</span><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'recoltants', 'id' => $key)) ?>" onclick="return confirm('Confirmez-vous la suppression du récoltant ?')" class="btn_supprimer">X</a></li>
								<?php endif; ?>
								<?php $i++; ?>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p>Aucun récoltant</p>
					<?php endif; ?>
				</div>

				<a href="<?php echo url_for('annuaire_selectionner', array('type' => 'recoltants')) ?>" class="btn"><img src="/images/boutons/btn_ajouter_recoltant.png" alt="Ajouter un récoltant" /></a>
			</div>

			<div class="bloc_annuaire">
				<h2 class="titre_section">Négociants (<?php echo count($annuaire->negociants) ?>)</h2>

				<div class="bloc">
					<?php if (count($annuaire->negociants) > 0): ?>
						<?php $i = 0; ?>
						<ul>
							<?php foreach ($annuaire->getAnnuaireSorted('negociants') as $key => $item): ?>
								<?php if($i % 2 == 0): ?>
									<li><?php echo $item ?> <span class="infos">(<?php echo $key; ?>)</span><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'negociants', 'id' => $key)) ?>" onclick="return confirm('Confirmez-vous la suppression du négociant ?')" class="btn_supprimer">X</a></li>
								<?php else: ?>
									<li class="alt"><?php echo $item ?> <span class="infos">(<?php echo $key; ?>)</span><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'negociants', 'id' => $key)) ?>" onclick="return confirm('Confirmez-vous la suppression du négociant ?')" class="btn_supprimer">X</a></li>
								<?php endif; ?>
								<?php $i++; ?>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p>Aucun négociant</p>
					<?php endif; ?>
				</div>

				<a href="<?php echo url_for('annuaire_selectionner', array('type' => 'negociants')) ?>" class="btn"><img src="/images/boutons/btn_ajouter_negociant.png" alt="Ajouter un négociant" /></a>
			</div>

			<div class="bloc_annuaire">
				<h2 class="titre_section">Caves coopératives (<?php echo count($annuaire->caves_cooperatives) ?>)</h2>
				<div class="bloc">
					<?php if (count($annuaire->caves_cooperatives) > 0): ?>
						<?php $i = 0; ?>
						<ul>
							<?php foreach ($annuaire->getAnnuaireSorted('caves_cooperatives') as $key => $item): ?>
								<?php if($i % 2 == 0): ?>
									<li><?php echo $item ?> <span class="infos">(<?php echo $key; ?>)</span><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'caves_cooperatives', 'id' => $key)) ?>" onclick="return confirm('Confirmez-vous la suppression de la cave coopérative ?')" class="btn_supprimer">X</a></li>
								<?php else: ?>
									<li class="alt"><?php echo $item ?> <span class="infos">(<?php echo $key; ?>)</span><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'caves_cooperatives', 'id' => $key)) ?>" onclick="return confirm('Confirmez-vous la suppression de la cave coopérative ?')" class="btn_supprimer">X</a></li>
								<?php endif; ?>
								<?php $i++; ?>
							<?php endforeach; ?>
						</ul>
					<?php else: ?>
						<p>Aucune cave coopérative</p>
					<?php endif; ?>
				</div>

				<a href="<?php echo url_for('annuaire_selectionner', array('type' => 'caves_cooperatives')) ?>" class="btn"><img src="/images/boutons/btn_ajouter_cave.png" alt="Ajouter une cave coopérative" /></a>
			</div>

			<div class="bloc_annuaire">
				<h2 class="titre_section">Commerciaux (<?php echo count($annuaire->commerciaux) ?>)</h2>
				<div class="bloc">
					<?php if (count($annuaire->commerciaux) > 0): ?>
						<?php $i = 0; ?>
						<ul>
							<?php foreach ($annuaire->getAnnuaireSorted('commerciaux') as $key => $item): ?>
								<?php if($i % 2 == 0): ?>
									<li><?php echo $key ?><?php if ($item): ?> <span class="infos"><?php echo $item; ?></span><?php endif; ?><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'commerciaux', 'id' => $key)) ?>" onclick="return confirm('Confirmez-vous la suppression du commercial ?')" class="btn_supprimer">X</a></li>
								<?php else: ?>
									<li class="alt"><?php echo $key ?><?php if ($item): ?> <span class="infos"><?php echo $item; ?></span><?php endif; ?><a href="<?php echo url_for('annuaire_supprimer', array('type' => 'commerciaux', 'id' => $key)) ?>" onclick="return confirm('Confirmez-vous la suppression du commercial ?')" class="btn_supprimer">X</a></li>
								<?php endif; ?>
								<?php $i++; ?>
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
</div>

<ul id="btn_etape" class="btn_prev_suiv">
	<li><a href="<?php echo url_for('mon_espace_civa_vrac', array('identifiant' => $compte->getIdentifiant())) ?>"><img src="/images/boutons/btn_retour_espace_contrats.png" alt="Retourner à l'espace contrats" /></a></li>
</ul>
