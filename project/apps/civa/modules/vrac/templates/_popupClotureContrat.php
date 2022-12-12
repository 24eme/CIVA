<div id="popup_cloture_contrat" class="popup_ajout" title="Cloture de votre contrat">
    <form id="cloture_contrat_form" method="post" action="<?php echo url_for('vrac_cloture', $vrac) ?>">
        <p>
            Tous les produits du contrat sont cloturés.<br /><br />
            Confirmez-vous la cloture général de votre contrat ?<br />
        </p>
        <br />
        <?php 
			if($validation->hasPoints()) {
				include_partial('global/validation', array('validation' => $validation)); 
			}
		?>
        <div id="btns" class="clearfix" style="text-align: center; margin-top: 8px;">
            <input type="image" src="/images/boutons/btn_valider.png" alt="Cloturer votre contrat" name="boutons[next]" id="clotureContrat_OK" class="valideDS_OK" />
            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png" /></a>
        </div>
    </form>
</div>
