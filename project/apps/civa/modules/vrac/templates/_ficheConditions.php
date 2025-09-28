<?php use_helper('vrac') ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 280px;">Conditions
				<?php if (!$fiche): ?><a href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'etape' => VracEtapes::ETAPE_CONDITIONS)) ?>" style="float:right;text-decoration: none;font-size:13px;padding-top:1px;">Modifier</a><?php endif; ?>
			</th>
		</tr>
	</thead>
	<tbody>
        <?php if($vrac->exist('prix_non_determine')): ?>
        <tr>
            <td>
                Prix non determiné
            </td>
            <td class="<?php echo isVersionnerCssClass($vrac, 'prix_non_determine') ?>">
                <?php echo ($vrac->prix_non_determine)? nl2br($vrac->prix_non_determine) : 'Aucun'; ?>
            </td>
        </tr>
        <?php endif; ?>
        <?php if($vrac->exist('vendeur_frais_annexes')): ?>
        <tr>
			<td>
				Frais annexes en sus à la charge du vendeur
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'vendeur_frais_annexes') ?>">
				<?php echo ($vrac->vendeur_frais_annexes)? nl2br($vrac->vendeur_frais_annexes) : 'Aucun'; ?>
			</td>
		</tr>
        <tr>
			<td>
				CVO AOC à la charge du vendeur
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'vendeur_frais_annexes') ?>">
				<?php echo $vrac->getTauxCvo(); ?> € HT/hl <small class="noprint" style="font-size: 12px; color: #666; margin-left: 10px;">(<a target="_blank" href="/drm/doc/docs/Organisation_du_marche_2024_2025.pdf">Organisation du marché 2024/2025</a>)</small>
			</td>
		</tr>
		<?php endif; ?>
        <?php if($vrac->exist('acheteur_primes_diverses')): ?>
        <tr>
			<td>
				Primes diverses à la charge de l'acheteur
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'acheteur_primes_diverses') ?>">
				<?php echo ($vrac->acheteur_primes_diverses)? nl2br($vrac->acheteur_primes_diverses) : 'Aucune'; ?>
			</td>
		</tr>
		<?php endif; ?>
        <tr>
			<td>
				Délais de paiement
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'conditions_paiement') ?>">
				<?php echo ($vrac->conditions_paiement)? $vrac->conditions_paiement : 'Aucun'; ?>
			</td>
		</tr>
        <?php if($vrac->exist('clause_resiliation')): ?>
        <tr>
			<td>
				Résiliation hors cas de force majeur
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'clause_resiliation') ?>">
				<?php echo ($vrac->clause_resiliation)? nl2br($vrac->clause_resiliation) : 'Aucune'; ?>
			</td>
		</tr>
		<?php endif; ?>
		<?php if($vrac->exist('clause_reserve_propriete')): ?>
		<tr>
			<td>
				Clause de réserve de propriété
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'clause_reserve_propriete') ?>">
				<?php if($vrac->clause_reserve_propriete): ?><strong>Oui</strong><?php else: ?>Non<?php endif; ?> <small class="noprint" style="font-size: 12px; color: #666; margin-left: 10px;">(Les modalités de cette clause sont indiquées au <a href="<?php echo url_for('vrac_pdf_annexe', array("type_contrat" => $vrac->type_contrat, "clause_reserve_propriete" => true)) ?>">verso du contrat</a>)</small>

			</td>
		</tr>
		<?php endif; ?>
        <?php if($vrac->exist('clause_mandat_facturation')): ?>
		<tr>
			<td>
				Mandat de facturation
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'clause_mandat_facturation') ?>">
				<?php if($vrac->clause_mandat_facturation): ?><strong>Oui</strong><?php else: ?>Non<?php endif; ?><?php if($vrac->clause_mandat_facturation): ?><small class="noprint" style="font-size: 12px; color: #666; margin-left: 10px;">(Le vendeur donne mandat à l’acheteur d’établir en son nom et pour son compte, les bordereaux récapitulatifs de règlement ou factures suivant les modalités convenues entre les parties dans le mandat).</small><?php endif; ?>
			</td>
		</tr>
		<?php endif; ?>
        <?php if($vrac->exist('clause_evolution_prix') && $vrac->isPluriannuelCadre()): ?>
        <tr>
			<td>
				Critères et modalités d’évolution des prix
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'clause_evolution_prix') ?>">
				<?php echo ($vrac->clause_evolution_prix)? nl2br($vrac->clause_evolution_prix) : 'Aucun'; ?>
			</td>
		</tr>
		<?php endif; ?>
		<?php if($vrac->exist('suivi_qualitatif')): ?>
		<tr>
			<td>
				<label>Suivi qualitatif</label>
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'suivi_qualitatif') ?>">
				<?php if($vrac->suivi_qualitatif): ?><strong>Oui</strong><?php else: ?>Non<?php endif; ?>
			</td>
		</tr>
		<?php endif; ?>
		<?php if($vrac->exist('delais_retiraison')): ?>
        <tr>
			<td>
				<label>Délai maximum de retiraison</label>
			</td>
			<td>
				<?php echo ($delais = $vrac->getDelaisRetiraison())? $delais : VracClient::DELAIS_RETIRAISON_AUCUN; ?>
			</td>
		</tr>
        <?php endif; ?>
        <tr>
			<td>
				Autres clauses particulières
			</td>
			<td class="<?php echo isVersionnerCssClass($vrac, 'conditions_particulieres') ?>">
				<?php echo ($vrac->conditions_particulieres)? $vrac->conditions_particulieres : 'Aucune'; ?>
			</td>
		</tr>
		<?php if($vrac->isInterne()): ?>
        <tr>
                <td>
                        <label>Interne</label>
                </td>
                <td>
                        <strong>Oui</strong>
                </td>
        </tr>
        <?php endif; ?>
	</tbody>
</table>
