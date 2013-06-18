<?php include_partial('recapitulatifDs', array('ds_principale' => $ds_principale, 'ds_client' => $ds_client));  ?>

<?php include_partial('generationDuPdf', array('ds_principale' => $ds_principale)) ?>

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="<?php echo url_for('mon_espace_civa'); ?>">
			<img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
            <a href="<?php echo url_for('ds_export_pdf', $ds_principale);?>">
		<input type="image" src="/images/boutons/btn-recevoir-ma-ds-par-email.png" alt="Recevoir ma ds par email" name="boutons[recevoir]" id="recevoir">
            </a>
	</li>
	<li class="previsualiser">
            <a href="<?php echo url_for('ds_export_pdf', $ds_principale);?>">
		<input type="image" src="/images/boutons/btn_visualiser.png" alt="Prévisualiser" name="boutons[previsualiser]" id="previsualiser">
            </a>            
    </li>
</ul>