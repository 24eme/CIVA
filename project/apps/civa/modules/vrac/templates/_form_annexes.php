<p class="intro_contrat_vrac">Veuillez ajouter ici les <strong>annexes applicables</strong> au contrat.</p>

<?php if(!$vrac->isPapier()): ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 212px;">Téléversement</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<?php echo $form['fichier']->renderLabel() ?>
			</td>
			<td>
				<span><?php echo $form['fichier']->renderError() ?></span>
				<?php echo $form['fichier']->render() ?>
                <?php echo $form['libelle']->render(['placeholder' => 'Saisir ici le nom de l\'annexe']) ?>
                <button type="submit" name="submitAndReload" class="btn_majeur btn_vert" style="padding:0 5px;">
                    <svg style="position:relative;top:2px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                    Ajouter
                </button>
			</td>
		</tr>
	</tbody>
</table>
<?php endif; ?>

<?php if(!$vrac->isPapier()): ?>
<p class="intro_contrat_vrac">liste des annexes applicables au contrat.</p>
<?php include_partial('vrac/ficheAnnexes', array('vrac' => $vrac, 'fiche' => false, 'edit' => true)); ?>
<?php endif; ?>
