<form method="post" action="">
<?php echo $form->renderHiddenFields() ?>
<?php echo $form->renderGlobalErrors() ?>
<table>
<tr>
<th>Nom</th>
<th>Email</th>
<?php foreach($compte->getDroitsTiers() as $droit): ?>
	<th><?php echo $droit ?></th>
<?php endforeach; ?>
</tr>
<?php foreach($form->getComptes() as $compte_personne): ?>
	<tr style="<?php echo ($compte_personne->isCompteSociete()) ? "font-weight: bold;" : null ?>">
	<td><a href="<?php echo url_for('compte_personne_modifier', array('login' => $compte_personne->login)) ?>"><?php echo $compte_personne->nom; ?></a></td>
	<td><?php echo $compte_personne->email; ?></td>
	<?php foreach($compte_personne->getDroitsTiers() as $key => $libelle): ?>
	<td>
		<input type="checkbox" id="comptes_droits<?php echo $compte_personne->_id ?>_droits_<?php echo $key ?>" value="<?php echo $key ?>" name="comptes_droits[<?php echo $compte_personne->_id ?>][droits][]" <?php echo (in_array($key, $compte_personne->droits->toArray())) ? 'checked="checked"' : null ?> <?php echo ($compte_personne->isCompteSociete()) ? 'disabled="disabled"' : null ?>>
	</td>
	<?php endforeach; ?>
	</tr>
<?php endforeach; ?>
</table>
<input type="submit" value="valider" />
</form>

<a href="<?php echo url_for('compte_personne_ajouter') ?>">Ajouter</a>