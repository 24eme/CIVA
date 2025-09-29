<?php use_helper('vrac') ?>

<table class="validation table_donnees">
    <thead>
        <tr>
            <th style="width: 280px;">
                Annexes
                <?php if (!$fiche&&!$edit): ?><a href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'etape' => VracEtapes::ETAPE_ANNEXES)) ?>" style="float:right;text-decoration: none;font-size:13px;padding-top:1px;">Modifier</a><?php endif; ?>
            </th>
            <td></td>
            <td></td>
        </tr>
    </thead>
	<tbody>
	<?php
    if ($vrac->hasAnnexes()):
    $annexes = $vrac->getAllAnnexesFilename();
    ?>
        <?php foreach($annexes as $annexeFilename => $annexe): ?>
		<tr class="<?php echo isVersionnerCssClass($vrac, "_attachments/$annexeFilename/content_type") ?>">
			<td colspan="2">
                <a class=" " style="padding:0 5px;" href="<?php echo url_for('vrac_annexe', ['sf_subject' => $vrac, 'operation' => 'visualiser', 'annexe' => $annexe]) ?>"><svg style="position:relative;top:2px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg></a>
				<?php echo str_replace(VracClient::VRAC_PREFIX_ANNEXE,'',$annexeFilename) ?>
			</td>
            <td style="text-align: right">
                <?php if($edit): ?>
                <a onclick="return confirm('Confirmez-vous la suppression de l\'annexe <?php echo $annexe ?> ?')" style="padding:0 5px;" href="<?php echo url_for('vrac_annexe', ['sf_subject' => $vrac, 'operation' => 'supprimer', 'annexe' => $annexe]) ?>">
		<svg style="position:relative;top:1px;color:#5b5b5b;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"></path><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"></path></svg></a>

            <?php endif; ?>
            </td>
		</tr>
        <?php endforeach; ?>
        <?php else: ?>
	</tbody>
</table>
<p style="font-style: italic; color: #666; margin:10px 0;">Aucune annexe téléversée.</p>
<?php endif; ?>
