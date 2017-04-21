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
                        <th>Code de création</th>
						<th>Droits</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach($form->getComptes() as $compte): ?>
                        <?php $formCompte= $form[$compte->_id]; ?>
						<tr>
						<td><a href="<?php echo url_for('compte_personne_modifier', array('login' => $compte->login)) ?>"><?php echo $compte->nom_a_afficher; ?></a> <?php if($compte->getCompteType() == CompteClient::TYPE_COMPTE_INTERLOCUTEUR): ?>(modifier)<?php endif; ?></td>
						<td style="text-align: left"><?php echo $compte->login ?></td>
						<td style="text-align: left"><?php echo $compte->email; ?></td>
                        <td style="text-align: center">
							<?php if($compte->getStatutTeledeclarant() == CompteClient::STATUT_TELEDECLARANT_NOUVEAU): ?>
                                <?php echo str_replace('{TEXT}', '', $compte->mot_de_passe); ?>
							<?php else: ?>
								Compte déjà créé
							<?php endif; ?>
						</td>
                        <td style="text-align: left; padding: 5px 5px;">
                            <ul>
						<?php foreach($compte->getSociete()->droits as $droit): ?>
							<li>
                                <?php echo $formCompte->renderError(); ?>
                                <label><input type="checkbox" value="<?php echo $droit ?>" name="<?php echo $formCompte["droits"]->renderName() ?>[]" <?php echo (in_array($droit, $compte->droits)) ? 'checked="checked"' : null ?>>&nbsp;&nbsp;<?php echo $droit; ?></label>
                            </li>
						<?php endforeach; ?>
                            </ul>
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
    <ul id="btn_etape" class="btn_prev_suiv clearfix">
        <li><a href="<?php echo url_for('mon_espace_civa', array('identifiant' => $compte->identifiant)) ?>"><img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à mon espace CIVA" /></a></li>
    </ul>
</form>
