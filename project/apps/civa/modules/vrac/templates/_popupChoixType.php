<?php $id = "popup_choix_typeVrac".((isset($papier) && $papier) ? "Papier" : null); ?>
<?php $idSelect = "select_createur".((isset($papier) && $papier) ? "Papier" : null); ?>

<div id="<?php echo $id ?>" class="popup_ajout " title="Création du contrat">
    <form method="post" action="" id="contrats_vrac">
    		<div class="form_col selecteur bloc_infos" style="width:auto; border:none;margin:0;">
                <div class="ligne_form" style="margin:0 0 25px 0;">
                    <?php $etablissements = VracClient::getInstance()->getEtablissements($sf_user->getCompte()->getSociete()); ?>
                    <select style="margin: 0; width: 100%;" id="<?php echo $idSelect ?>">
                    <?php foreach($etablissements as $etablissement): ?>
                        <?php if(!VracSecurity::getInstance($sf_user->getCompte(), null)->isAuthorizedTiers($etablissement, VracSecurity::CREATION)): continue; endif; ?>
                        <option value="<?php echo $etablissement->_id ?>"><?php echo $etablissement->nom ?> <?php echo $etablissement->cvi ?> <?php echo EtablissementFamilles::$familles[$etablissement->famille]; ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
				<div class="ligne_form" id="type_contrat_radio_list" style="margin:0;">
					<label class="bold" style="display:inline-block;margin:0;">Vous êtes :</label>
					<ul class="radio_list" style="margin:0;">
						<li><input type="radio" id="choix_type_vendeur" value="<?php echo url_for('vrac_selection_type', array('type' => 'vendeur', 'papier' => (isset($papier) && $papier) ? $papier : 0)) ?>" name="choix_type">&nbsp;<label for="choix_type_vendeur" style="display:inline-block;">Vendeur</label></li>
						<li><input type="radio" checked="checked" id="choix_type_acheteur" value="<?php echo url_for('vrac_selection_type', array('type' => 'acheteur', 'papier' => (isset($papier) && $papier) ? $papier : 0)) ?>" name="choix_type">&nbsp;<label for="choix_type_acheteur" style="display:inline-block;">Acheteur</label></li>
					</ul>
				</div>
			</div>
	        <div id="btns" class="clearfix" style="text-align: center; margin-top: 8px;">
	            <input type="image" src="/images/boutons/btn_valider.png" alt="Signer votre contrat" name="boutons[next]" class="" />
	            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
	        </div>
	        <script type="text/javascript">
				$("#<?php echo $id ?> form").submit(function() {
					var url = $('#<?php echo $id ?> form input[type=radio]:checked').val();

					document.location.href = url+"&createur="+$('#<?php echo $idSelect ?>').val();
					return false;
				});
			</script>
    </form>
</div>
