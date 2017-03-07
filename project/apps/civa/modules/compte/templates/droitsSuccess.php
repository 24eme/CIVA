<form method="post" action="">
<?php echo $form->renderHiddenFields() ?>
<?php echo $form->renderGlobalErrors() ?>
 	<h2 class="titre_principal">Gestion des droits</h2>
    <div class="clearfix" id="application_dr">
        <div id="nouvelle_declaration" style="width: 100%;">
                <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto">
                	<thead>
					<tr>
						<th style="width: auto;">Nom</th>
						<th>Login</th>
						<th>Email</th>
						<?php foreach($compte->droits as $droit): ?>
							<th><?php echo $droit ?></th>
						<?php endforeach; ?>
						<th>Code de création</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($form->getComptes() as $compte_personne): ?>
						<tr style="<?php echo ($compte_personne->isSocieteContact()) ? "font-weight: bold;" : null ?>">
						<td><a href="<?php echo url_for('compte_personne_modifier', array('login' => $compte_personne->login)) ?>"><?php echo $compte_personne->nom; ?></a></td>
						<td><?php echo $compte_personne->login ?></td>
						<td><?php echo $compte_personne->email; ?></td>
						<?php foreach($compte_personne->droits as $key => $libelle): ?>
						<td style="text-align: center;">
							<input type="checkbox" id="comptes_droits<?php echo $compte_personne->_id ?>_droits_<?php echo $key ?>" value="<?php echo $key ?>" name="comptes_droits[<?php echo $compte_personne->_id ?>][droits][]" <?php echo (in_array($key, $compte_personne->droits->toArray())) ? 'checked="checked"' : null ?>>
						</td>
						<?php endforeach; ?>
						<td>
							<?php if($compte_personne->getStatutTeledeclarant() == CompteClient::STATUT_TELEDECLARANT_NOUVEAU): ?>
							<?php //echo $compte_personne->getCodeCreation() ?>
							<?php else: ?>
								Compte déjà créé
							<?php endif; ?>
						</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

                <div style="margin-left: 0;" class="ligne_form ligne_btn">
                    <a href="<?php echo url_for('compte_personne_ajouter') ?>">Ajouter un compte</a>
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
        </div>
    </div>
    <li><a href="<?php echo url_for('mon_espace_civa', array('identifiant' => $compte->identifiant)) ?>"><img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à mon espace CIVA" /></a></li>
    <ul id="btn_etape" class="btn_prev_suiv clearfix">
    </ul>
</form>
