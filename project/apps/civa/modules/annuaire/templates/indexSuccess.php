<div class="clearfix">
		<h1>Récoltants (<?php echo count($annuaire->recoltants) ?>)</h1>
		<br />
		<?php if (count($annuaire->recoltants) > 0): ?>
		<ul>
		<?php foreach ($annuaire->recoltants as $key => $item): ?>
			<li><?php echo $item ?> <small style="font-size: 10px;">(<?php echo $key ?>)</small>&nbsp;<a href="<?php echo url_for('annuaire_supprimer', array('type' => 'recoltants', 'id' => $key)) ?>">X</a></li>
		<?php endforeach; ?>
		</ul>
		<?php else: ?>
		<i>Aucun récoltants</i>
		<?php endif; ?>
		<br /><br />
		<h1>Négociants (<?php echo count($annuaire->negociants) ?>)</h1>
		<br />
		<?php if (count($annuaire->negociants) > 0): ?>
		<ul>
		<?php foreach ($annuaire->negociants as $key => $item): ?>
			<li><?php echo $item ?> <small style="font-size: 10px;">(<?php echo $key ?>)</small>&nbsp;<a href="<?php echo url_for('annuaire_supprimer', array('type' => 'negociants', 'id' => $key)) ?>">X</a></li>
		<?php endforeach; ?>
		</ul>
		<?php else: ?>
		<i>Aucun négociants</i>
		<?php endif; ?>
		<br /><br />
		<h1>Caves coopératives (<?php echo count($annuaire->caves_cooperatives) ?>)</h1>
		<br />
		<?php if (count($annuaire->caves_cooperatives) > 0): ?>
		<ul>
		<?php foreach ($annuaire->caves_cooperatives as $key => $item): ?>
			<li><?php echo $item ?> <small style="font-size: 10px;">(<?php echo $key ?>)</small>&nbsp;<a href="<?php echo url_for('annuaire_supprimer', array('type' => 'caves_cooperatives', 'id' => $key)) ?>">X</a></li>
		<?php endforeach; ?>
		</ul>
		<?php else: ?>
		<i>Aucun négociants</i>
		<?php endif; ?>
		<br /><br />
		<p><a href="<?php echo url_for('@annuaire_selectionner') ?>">+ Ajouter une entrée</a></p>
</div>

