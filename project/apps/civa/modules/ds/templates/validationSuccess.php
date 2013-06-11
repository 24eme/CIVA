<?php 
include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds_principale, 'etape' => 5)); 
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

<div style="display: none" id="popup_loader" title="Génération du PDF">
    <div class="popup-loading">
    <p>La génération de votre PDF est en cours.<br />Merci de patienter.<br /><small>La procédure peut prendre 30 secondes</small></p>
    </div>
</div>

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



