<form method="post" action="">
<?php echo $form->renderHiddenFields() ?>
<?php echo $form->renderGlobalErrors() ?>
 	<h2 class="titre_principal">Gestion des droits</h2>

    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration" style="width: 100%;">
                <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto">
                	<thead>
					<tr>
						<th>Nom</th>
						<th>Email</th>
						<?php foreach($compte->getDroitsTiers() as $droit): ?>
							<th><?php echo $droit ?></th>
						<?php endforeach; ?>
					</tr>
					</thead>
					<tbody>
					<?php foreach($form->getComptes() as $compte_personne): ?>
						<tr style="<?php echo ($compte_personne->isCompteSociete()) ? "font-weight: bold;" : null ?>">
						<td><a href="<?php echo url_for('compte_personne_modifier', array('login' => $compte_personne->login)) ?>"><?php echo $compte_personne->nom; ?></a></td>
						<td><?php echo $compte_personne->email; ?></td>
						<?php foreach($compte_personne->getDroitsTiers() as $key => $libelle): ?>
						<td style="text-align: center;">
							<input type="checkbox" id="comptes_droits<?php echo $compte_personne->_id ?>_droits_<?php echo $key ?>" value="<?php echo $key ?>" name="comptes_droits[<?php echo $compte_personne->_id ?>][droits][]" <?php echo (in_array($key, $compte_personne->droits->toArray())) ? 'checked="checked"' : null ?>>
						</td>
						<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

                <div class="ligne_form ligne_btn">
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
        </div>
        <!-- fin #nouvelle_declaration -->

        <!-- #precedentes_declarations -->

        <!-- fin #precedentes_declarations -->
    </div>
    <!-- fin #application_dr -->
</form>