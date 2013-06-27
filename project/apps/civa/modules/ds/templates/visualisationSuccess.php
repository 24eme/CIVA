<ul id="onglets_majeurs" class="clearfix">
    <li class="ui-tabs-selected"><a href="#recap_total_ds">Déclaration de Stocks <?php echo $ds_principale->getAnnee(); ?></a></li>
                    <a href="" class="msg_aide" rel="help_popup_ds_validation" title="Message aide"></a>
</ul>

<?php
include_partial('recapitulatifDs', array('ds_principale' => $ds_principale, 'ds_client' => $ds_client)); 
?>
<?php include_partial('generationDuPdf', array('ds_principale' => $ds_principale)) ?>

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="<?php echo url_for('mon_espace_civa'); ?>">
			<img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
        <?php if($ds_principale->isValidee()): ?>
	<li class="suiv">
            <a href="<?php echo url_for('ds_export_pdf', $ds_principale);?>">
		<input type="image" src="/images/boutons/btn-recevoir-ma-ds-par-email.png" alt="Recevoir ma ds par email" name="boutons[recevoir]" id="recevoir">
            </a>
	</li>
        <?php endif; ?>
	<li class="previsualiser">
            <a href="<?php echo url_for('ds_export_pdf', $ds_principale);?>">
		<input type="image" src="/images/boutons/btn_previsualiser.png" alt="Prévisualiser" name="boutons[previsualiser]" id="previsualiser">
            </a>            
    </li>
</ul>