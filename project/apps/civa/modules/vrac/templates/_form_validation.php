<?php use_helper('Float') ?>
<?php use_helper('Date') ?>

<p class="intro_contrat_vrac">Vous trouverez ci-dessous le récapitulatif du contrat, les informations relatives aux soussignés et les quantités de produit concernées. <br />Saisissez ici les éventuelles conditions du contrat.</p>
<?php include_partial('vrac/soussignes', array('vrac' => $vrac, 'user' => $user, 'fiche' => false)) ?>

<table class="validation table_donnees" style="margin: 0 0 20px;">
	<thead>
		<tr>
			<th>Produits</th>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<th class="bouteille" style="text-align: center">Nb bouteilles</th>
			<th class="centilisation" style="text-align: center">Centilisation</th>
			<?php endif; ?>
			<th class="volume" style="text-align: center">Volume</th>
			<th class="prix" style="text-align: center">Prix</th>
			<th class="date_retiraison_limite" style="text-align: center; width: 100px;">Début de retiraison</th>
			<th class="date_retiraison_limite" style="text-align: center; width: 100px;">Limite de retiraison</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$counter = 0;
			$volumeTotal = 0;
			foreach ($vrac->declaration->getActifProduitsDetailsSorted() as $details):
			foreach ($details as $detail):
			$alt = ($counter%2);
			$volumeTotal += ($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose;
		?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td>
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?>  <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong><?php echo ($detail->exist('label') && $detail->get("label"))? " ".VracClient::$label_libelles[$detail->get("label")] : ""; ?>
			</td>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="bouteille"><?php echo $detail->nb_bouteille ?></td>
			<td class="centilisation"><?php echo VracClient::getLibelleCentilisation($detail->centilisation) ?></td>
			<?php endif; ?>
			<td class="volume">
				<?php echoFloat(($vrac->isInModeSurface())? $detail->surface_propose : $detail->volume_propose) ?>&nbsp;<?php echo ($vrac->isInModeSurface())? 'ha' : 'hl' ?>
			</td>
			<td class="prix">
				<?php if ($detail->prix_unitaire): ?><?php echoFloat($detail->prix_unitaire) ?>&nbsp;&euro;/<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>blle<?php else: ?>hl<?php endif; ?><?php endif; ?>
			</td>
            <td class="date_retiraison_limite" style="text-align: center;">
				<?php if($detail->retiraison_date_debut && !$vrac->isPluriannuelCadre()): ?>
                <?php echo format_date($detail->retiraison_date_debut, 'dd/MM/yyyy') ?>
				<?php elseif($detail->retiraison_date_debut): ?>
					<?php echo format_date('1970-'.$detail->retiraison_date_debut, 'dd/MM') ?>
                <?php endif; ?>
			</td>
            <td class="date_retiraison_limite" style="text-align: center;">
                <?php if($detail->retiraison_date_limite && !$vrac->isPluriannuelCadre()): ?>
                    <?php echo format_date($detail->retiraison_date_limite, 'dd/MM/yyyy') ?>
                <?php elseif($detail->retiraison_date_limite): ?>
	                <?php echo format_date('1970-'.$detail->retiraison_date_limite, 'dd/MM') ?>
                <?php endif;  ?>
			</td>
		</tr>
		<?php
			$counter++;  endforeach;
			endforeach;
		?>
		<tr<?php if (!$alt): ?> class="alt"<?php endif; ?>>
			<td style="text-align: right;"<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?> colspan="3"<?php endif; ?>><strong><?php echo ($vrac->isInModeSurface())? 'Surface' : 'Volume' ?> total</strong></td>
			<td class="volume">
				<?php echoFloat($volumeTotal) ?>&nbsp;hl
			</td>
			<td colspan="3"></td>
		</tr>
	</tbody>
</table>

<?php if(!$vrac->isPapier()): ?>
<table class="validation table_donnees" style="margin:0 0 20px;">
	<thead>
		<tr>
			<th style="width: 212px;">Conditions</th>
		</tr>
	</thead>
	<tbody>
        <?php if($vrac->exist('vendeur_frais_annexes')): ?>
        <tr>
			<td>
				Frais annexes en sus à la charge du vendeur
			</td>
			<td>
				<?php echo ($vrac->vendeur_frais_annexes)? nl2br($vrac->vendeur_frais_annexes) : 'Aucun'; ?>
			</td>
		</tr>
		<?php endif; ?>
        <?php if($vrac->exist('acheteur_primes_diverses')): ?>
        <tr class="alt">
			<td>
				Primes diverses à la charge de l'acheteur
			</td>
			<td>
				<?php echo ($vrac->acheteur_primes_diverses)? nl2br($vrac->acheteur_primes_diverses) : 'Aucune'; ?>
			</td>
		</tr>
		<?php endif; ?>
        <?php if($vrac->exist('clause_resiliation')): ?>
        <tr>
			<td>
				Résiliation hors cas de force majeur
			</td>
			<td>
				<?php echo ($vrac->clause_resiliation)? nl2br($vrac->clause_resiliation) : 'Aucune'; ?>
			</td>
		</tr>
		<?php endif; ?>
        <tr class="alt">
			<td>
				Délais de paiement
			</td>
			<td>
				<?php echo ($vrac->conditions_paiement)? $vrac->conditions_paiement : 'Aucun'; ?>
			</td>
		</tr>
		<?php if($vrac->exist('clause_reserve_propriete')): ?>
		<tr>
			<td>
				<label>Clause de réserve de propriété</label>
			</td>
			<td>
				<?php if($vrac->clause_reserve_propriete): ?><strong>Oui</strong><?php else: ?>Non<?php endif; ?> <small class="noprint" style="font-size: 12px; color: #666; margin-left: 10px;">(Les modalités de cette clause sont indiquées au <a href="<?php echo url_for('vrac_pdf_annexe', array("type_contrat" => $vrac->type_contrat, "clause_reserve_propriete" => true)) ?>">verso du contrat</a>)</small>

			</td>
		</tr>
		<?php endif; ?>
        <?php if($vrac->exist('clause_mandat_facturation')): ?>
		<tr class="alt">
			<td>
				<label>Mandat de facturation</label>
			</td>
			<td>
				<?php if($vrac->clause_mandat_facturation): ?><strong>Oui</strong><?php else: ?>Non<?php endif; ?><?php if($vrac->clause_mandat_facturation): ?><small class="noprint" style="font-size: 12px; color: #666; margin-left: 10px;">(Le vendeur donne mandat à l’acheteur d’établir en son nom et pour son compte, les bordereaux récapitulatifs de règlement ou factures suivant les modalités convenues entre les parties dans le mandat.</small><?php endif; ?>
			</td>
		</tr>
		<?php endif; ?>
		<?php if($vrac->isInterne()): ?>
                <tr class="alt">
                        <td>
                                <label>Interne</label>
                        </td>
                        <td>
                                <strong>Oui</strong>
                        </td>
                </tr>
                <?php endif; ?>
        <?php if($vrac->exist('clause_evolution_prix') && $vrac->isPluriannuelCadre()): ?>
        <tr>
			<td>
				Critères et modalités d’évolution des prix
			</td>
			<td>
				<?php echo ($vrac->clause_evolution_prix)? nl2br($vrac->clause_evolution_prix) : 'Aucun'; ?>
			</td>
		</tr>
		<?php endif; ?>
        <tr class="alt">
			<td>
				Autre clauses particulières
			</td>
			<td>
				<?php echo ($vrac->conditions_particulieres)? $vrac->conditions_particulieres : 'Aucune'; ?>
			</td>
		</tr>
	</tbody>
</table>
<?php endif; ?>

<?php if($vrac->isPapier()): ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 212px;">Contrat papier</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<?php echo $form['numero_papier']->renderLabel() ?>
			</td>
			<td>
				<span><?php echo $form['numero_papier']->renderError() ?></span>
				<?php echo $form['numero_papier']->render(array("autofocus" => "autofocus")) ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo $form['date_signature']->renderLabel() ?>
			</td>
			<td>
				<span><?php echo $form['date_signature']->renderError() ?></span>
				<?php echo $form['date_signature']->render(array('class' => 'datepicker')) ?>
			</td>
		</tr>
	</tbody>
</table>
<?php endif; ?>
<?php include_partial('vrac/popupConfirmeValidation', array('vrac' => $vrac)); ?>
