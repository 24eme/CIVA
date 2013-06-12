<?php 
use_helper('ds');
include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds_principale, 'etape' => 5)); 

foreach ($validation_dss as $id_ds => $validation_ds):
    if($validation_ds->isPoints()):
    ?>
    <h2><?php echo getTitleLieuStockageStock($ds_client->find($id_ds)); ?></h2>
    <?php
    endif;
include_partial('document_validation/validation', array('validation' => $validation_ds));
endforeach;
include_partial('recapitulatifDs', array('ds_principale' => $ds_principale, 'ds_client' => $ds_client)); 
?>

<ul id="btn_etape" class="btn_prev_suiv clearfix">
	<li class="prec">
		<a href="<?php echo url_for('ds_autre',$ds_principale); ?>">
			<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" />
		</a>
	</li>
	<li class="suiv">
		<a href="#">
			<img src="/images/boutons/btn_valider_final.png" alt="Valider votre déclaration" />
		</a>
	</li>
	<li class="previsualiser">
            <a href="<?php echo url_for('ds_export_pdf', $ds_principale);?>">
		<input type="image" src="/images/boutons/btn_previsualiser.png" alt="Prévisualiser" name="boutons[previsualiser]" id="previsualiser">
            </a>
		<a href="" class="msg_aide" rel="telecharger_pdf" title="Message aide"></a>
    </li>
</ul>
<?php include_partial('ds/generationDuPdf', array('ds' => $ds_principale)) ?>
<div id="popup_confirme_validation" class="popup_ajout" title="Validation de votre DR">
    <form method="post" action="">
        <p>
            Une fois votre déclaration validée, vous ne pourrez plus la modifier. <br /><br />
            Confirmer vous la validation de votre déclaration de récolte ? <br />
        </p>
        <div id="btns">
			<input type="image" src="/images/boutons/btn_valider.png" alt="Valider votre déclaration" name="boutons[next]" id="valideDR" class="valideDR_OK" />
            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
        </div>
    </form>
</div>



