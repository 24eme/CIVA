<div id="contrats_vrac">
	<h2 class="titre_principal">Historique de vos contrats de vente</h2>
	<a style="float: right; margin-right: 20px; bottom: 6px; color: #2A2A2A; text-decoration: none;" class="btn_majeur btn_petit btn_jaune" href="<?php echo url_for('vrac_export_csv', array('identifiant' => $compte->getIdentifiant())) ?>">Exporter les contrats en CSV</a>
	<div class="fond">
		<form action="<?php echo url_for('vrac_historique', array('campagne' => $campagne, 'identifiant' => $compte->getIdentifiant())) ?>" method="GET">
			<ul class="filtres clearfix">
				<li><label for="statut">Type de contrat :</label><select id="type" name="type"><option value="">Tous</option><?php foreach ($types as $k => $s): ?><option value="<?php echo $k ?>"<?php if ($type == $k): ?> selected="selected"<?php endif; ?>><?php echo $s ?></option><?php endforeach; ?></select></li>
				<li><label for="statut">Statut :</label><select id="statut" name="statut"><option value="">Tous</option><?php foreach ($statuts as $k => $s): ?><option value="<?php echo $k ?>"<?php if ($statut == $k): ?> selected="selected"<?php endif; ?>><?php echo $s ?></option><?php endforeach; ?></select></li>
				<?php if(count($roles) > 1): ?>
				<li><label for="statut">En tant que :</label><select id="role" name="role"><option value="">Tous</option><?php foreach ($roles as $k => $s): ?><option value="<?php echo $k ?>"<?php if ($role == $k): ?> selected="selected"<?php endif; ?>><?php echo $s ?></option><?php endforeach; ?></select></li>
				<?php endif; ?>
				<li><label for="campagne">Campagne :</label><select id="campagne" name="campagne"><?php foreach ($campagnes as $c): ?><option value="<?php echo $c ?>"<?php if ($campagne == $c): ?> selected="selected"<?php endif; ?>><?php echo $c ?></option><?php endforeach; ?></select></li>
                <li><label for="campagne">Commercial :</label><select id="commercial" name="commercial"><option value="">Tous</option><?php foreach ($commerciaux as $k => $com): ?><option value="<?php echo $k ?>"<?php if ($commercial == $k): ?> selected="selected"<?php endif; ?>><?php echo $k ?></option><?php endforeach; ?></select></li>
				<li><button id="valide_form" style="cursor: pointer; background: none repeat scroll 0 0 transparent; border: 0 none;" type="submit"><img src="/images/boutons/btn_valider_2.png" alt="Continuer à l'étape suivante"></button></li>
			</ul>
		</form>
		<div id="espace_alsace_contrats">
		<?php if (count($vracs) > 0): ?>
		<?php include_partial('vrac/liste', array('vracs' => $vracs, 'tiers' => $sf_user->getDeclarantsVrac(), 'limite' => false, 'archive' => true)) ?>
		<?php else: ?>
		<p><i>Aucun contrat.</i></p>
		<?php endif; ?>
		</div>
	</div>
</div>
<ul id="btn_etape" class="btn_prev_suiv">
	<li><a href="<?php echo url_for('mon_espace_civa_vrac', array('identifiant' => $compte->getIdentifiant())) ?>"><img alt="Retourner à l'espace contrats" src="/images/boutons/btn_retour_espace_contrats.png"></a></li>
</ul>
