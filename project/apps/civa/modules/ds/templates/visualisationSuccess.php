<ul id="onglets_majeurs" class="clearfix">
    <li class="ui-tabs-selected"><a href="#recap_total_ds">Récapitulatif Déclaration de Stocks <?php echo $ds_principale->getAnnee();?></a></li>
</ul>

<?php
include_partial('recapitulatifDs', array('ds_principale' => $ds_principale, 'ds_client' => $ds_client)); 
?>
<?php include_partial('generationDuPdf', array('ds' => $ds_principale)) ?>

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="<?php echo url_for('mon_espace_civa'); ?>">
			<img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
        <?php if($ds_principale->isValidee()): ?>
        <div id="validation_ds">
            <li class="suiv">
                <div id="div-btn-email"><a href="" id="btn-email"></a></div>
            </li>
        </div>
        <?php endif; ?>
	<li class="previsualiser">
            <a href="<?php echo url_for('ds_export_pdf', $ds_principale);?>">
		<input type="image" src="/images/boutons/btn_previsualiser.png" alt="Prévisualiser" name="boutons[previsualiser]" id="previsualiser">
            </a>            
    </li>
</ul>
<?php include_partial('ds/envoiMailDS', array('ds' => $ds_principale,'message' => null)); ?>
