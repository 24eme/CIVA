<div id="contrats_vrac">
	<h2 class="titre_principal">Historique de vos contrats vrac</h2>
	
	<div class="fond">
		<ul class="filtres clearfix">
			<li><label for="statut">Statut :</label><select id="statut" name="statut"><option value="<?php echo url_for('vrac_historique', array('campagne' => $campagne)) ?>">Tous</option><?php foreach ($statuts as $k => $s): ?><option value="<?php echo url_for('vrac_historique_statut', array('campagne' => $campagne, 'statut' => $k)) ?>"<?php if ($statut == $k): ?> selected="selected"<?php endif; ?>><?php echo $s ?></option><?php endforeach; ?></select></li>
			<li><label for="campagne">Campagne :</label><select id="campagne"><?php foreach ($campagnes as $c): ?><option value="<?php echo url_for('vrac_historique', array('campagne' => $c)) ?>"<?php if ($campagne == $c): ?> selected="selected"<?php endif; ?>><?php echo $c ?></option><?php endforeach; ?></select></li>
		</ul>
		<div id="espace_alsace_contrats">
		<?php if (count($vracs) > 0): ?>
		<?php include_partial('vrac/liste', array('vracs' => $vracs, 'user' => $user, 'limite' => false, 'archive' => true)) ?>
		<?php else: ?>
		<p><i>Aucun contrat.</i></p>
		<?php endif; ?>
		</div>
	</div>
</div>
<ul id="btn_etape" class="btn_prev_suiv">
	<li><a href="<?php echo url_for('mon_espace_civa') ?>"><img alt="Retourner à l'espace contrats" src="/images/boutons/btn_retour_espace_contrats.png"></a></li>
</ul>
<script type="text/javascript">
$(document).ready(function () {
	$("#campagne").change(function() {
    	document.location.href = $(this).val();
	});
	$("#statut").change(function() {
    	document.location.href = $(this).val();
	});
});
</script>