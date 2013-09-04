<div class="clearfix">
		<h1>Acheteurs (<?php echo count($annuaire->acheteurs) ?>)</h1>
		<br />
		<?php if (count($annuaire->acheteurs) > 0): ?>
		<ul>
		<?php foreach ($annuaire->acheteurs as $key => $item): ?>
			<li><?php echo $item ?> <small style="font-size: 10px;">(<?php echo $key ?>)</small>&nbsp;<a href="<?php echo url_for('annuaire_supprimer', array('type' => 'acheteurs', 'id' => $key)) ?>">X</a></li>
		<?php endforeach; ?>
		</ul>
		<?php else: ?>
		<i>Aucun acheteur</i>
		<?php endif; ?>
		<br /><br />
		<h1>Vendeurs (<?php echo count($annuaire->vendeurs) ?>)</h1>
		<br />
		<?php if (count($annuaire->vendeurs) > 0): ?>
		<ul>
		<?php foreach ($annuaire->vendeurs as $key => $item): ?>
			<li><?php echo $item ?> <small style="font-size: 10px;">(<?php echo $key ?>)</small>&nbsp;<a href="<?php echo url_for('annuaire_supprimer', array('type' => 'vendeurs', 'id' => $key)) ?>">X</a></li>
		<?php endforeach; ?>
		</ul>
		<?php else: ?>
		<i>Aucun vendeur</i>
		<?php endif; ?>
		<br /><br />
		<p><a href="<?php echo url_for('@annuaire_ajouter') ?>">+ Ajouter une entrée</a></p>
</div>

