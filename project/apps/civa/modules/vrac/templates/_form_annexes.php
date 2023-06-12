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

<?php
$annexes = $vrac->getAllAnnexesFilename();
if ($annexes):
?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 212px;">Annexes</th>
		</tr>
	</thead><tbody>
        <?php foreach($annexes as $annexe): ?>
		<tr>
			<td>

			</td>
			<td width="465">
                <a class="btn_majeur btn_vert" style="padding:0 5px;" href="<?php echo url_for('vrac_annexe', ['sf_subject' => $vrac, 'operation' => 'visualiser', 'annexe' => $annexe]) ?>"><svg style="position:relative;top:2px;color:white;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg></a>
				<?php echo $annexe ?>
			</td>
            <td style="text-align: right">
                <a onclick="return confirm('Confirmez-vous la suppression de l\'annexe <?php echo $annexe ?> ?')" class="btn_majeur btn_noir" style="padding:0 5px;" href="<?php echo url_for('vrac_annexe', ['sf_subject' => $vrac, 'operation' => 'supprimer', 'annexe' => $annexe]) ?>">
		<svg style="position:relative;top:1px;color:white;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path></svg></a>
            </td>
		</tr>
        <?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>
