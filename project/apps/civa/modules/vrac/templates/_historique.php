<?php use_helper('Date') ?>
<?php use_helper('vrac') ?>
<table class="validation table_donnees" style="width: 600px;">
	<thead>
		<tr>
			<th colspan="2">Chronologie du contrat</th>
		</tr>
	</thead>
	<tbody>
        <?php foreach($vrac->historique as $histo): ?>
        <tr>
            <td style="text-align: left; width: 100px;"><?php echo format_date($histo->date, 'dd/MM/yyyy HH:mm'); ?></td>
            <td><?php echo ($histo->auteur) ? $vrac->getTypeTiersLibelle($histo->auteur) : "Automatique"; ?></td>
            <td><?php echo $histo->description; ?><?php if($histo->commentaire): ?> : <?php echo $histo->commentaire; ?><?php endif; ?><?php if($sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)): ?>
            <?php $mails = VracMailer::getInstance()->getMessagesByStatut($vrac->getRawValue(), $histo->statut, $histo->auteur, false); ?>
            <?php if (count($mails)): ?>
                <a href="" onclick="document.getElementById('contenu_mail').innerHTML = this.title; document.getElementById('contenu_mail').style.display = 'block'; return false;" title="<?php echo displayMail($mails); ?>" style="position: absolute; left: 630px; opacity: 0.5;">[voir le mail]</a>
            <?php endif; ?>
            <?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php $find = false; ?>
        <?php foreach(Vrac::$statuts_template_historique as $libelleStatut => $statut): ?>
            <?php if($statut == $histo->statut): $find = true; continue; ?><?php endif; ?>
            <?php if(!$find): continue; ?><?php endif; ?>
            <?php if(strpos($libelleStatut, "(isVendeurProprietaire)") !== false && !$vrac->isVendeurProprietaire()): continue; endif; ?>
            <tr class="text-muted">
                <td style="text-align: left; width: 100px;"></td>
                <td></td>
                <td><?php echo str_replace(" (isVendeurProprietaire)", "", $libelleStatut); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<pre style="background: #000; color: white; padding: 5px; overflow: scroll; display: none; opacity: 0.90;" id="contenu_mail"></pre>
