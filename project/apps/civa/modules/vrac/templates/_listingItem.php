<?php $item = $item->getRawValue(); ?>
<tr<?php if($alt): ?> class="alt"<?php endif; ?>>
	<td><?php echo $item->valide->statut ?></td>
	<td><?php echo $item->numero_contrat ?></td>
	<td>
		Acheteur : <strong><?php echo $item->acheteur->raison_sociale; ?></strong><br />
		Vendeur : <strong><?php echo $item->vendeur->raison_sociale; ?></strong>
	</td>
	<td class="actions"><a href="<?php echo url_for('vrac_acces', $item) ?>">Accéder</a> | <a href="<?php echo url_for('vrac_supprimer', $item) ?>">X</a></td>
</tr>