<h1>Historique de vos contrats pour la campagne <span><select id="campagne"><?php foreach ($campagnes as $c): ?><option value="<?php echo url_for('vrac_historique', array('campagne' => $c)) ?>"<?php if ($campagne == $c): ?> selected="selected"<?php endif; ?>><?php echo $c ?></option><?php endforeach; ?></select></span></h1>
<p>Statut : <span><select id="statut"><option value="<?php echo url_for('vrac_historique', array('campagne' => $campagne)) ?>">--</option><?php foreach ($statuts as $k => $s): ?><option value="<?php echo url_for('vrac_historique_statut', array('campagne' => $campagne, 'statut' => $k)) ?>"<?php if ($statut == $k): ?> selected="selected"<?php endif; ?>><?php echo $s ?></option><?php endforeach; ?></select></span></p>
<?php if (count($vracs) > 0): ?>
<?php include_partial('vrac/liste', array('vracs' => $vracs, 'user' => $user, 'limite' => false, 'archive' => true)) ?>
<?php else: ?>
<p><i>Aucun contrat.</i></p>
<?php endif; ?>
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