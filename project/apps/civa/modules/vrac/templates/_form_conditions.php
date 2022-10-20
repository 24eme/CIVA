<?php use_helper('Float') ?>
<?php
    if($form->getObject()->isPluriannuelCadre()) {
        $datepickerClass = 'smalldatepicker';
        $header = 'Période';
    } else {
        $datepickerClass = 'datepicker';
        $header = 'Date';
    }
?>
<p class="intro_contrat_vrac">Veuillez saisir ici les conditions applicables au contrat.</p>

<table class="validation table_donnees">
	<thead>
		<tr>
			<th>Produits</th>
			<th class="date_retiraison" style="text-align: center"><?php echo $header?> début retiraison</th>
			<th class="date_retiraison" style="text-align: center"><?php echo $header?> limite retiraison</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$counter = 0;
    		foreach ($form['produits_retiraisons'] as $key => $embedForm) :
    			$detail = $vrac->get($key);
			    $alt = ($counter%2);
		?>
		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
			<td>
				<?php echo $detail->getLibelleSansCepage(); ?> <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?>  <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong><?php echo ($detail->exist('label') && $detail->get("label"))? " ".VracClient::$label_libelles[$detail->get("label")] : ""; ?>
			</td>
			<td class="date_retiraison">
    			<span><?php echo $embedForm['retiraison_date_debut']->renderError() ?></span>
    			<?php echo $embedForm['retiraison_date_debut']->render(array('class' => 'input_date '.$datepickerClass)) ?>
			</td>
			<td class="date_retiraison">
    			<span><?php echo $embedForm['retiraison_date_limite']->renderError() ?></span>
    			<?php echo $embedForm['retiraison_date_limite']->render(array('class' => 'input_date '.$datepickerClass)) ?>
			</td>
		</tr>
		<?php
			$counter++;
			endforeach;
		?>

	</tbody>
</table>

<?php if(!$vrac->isPapier()): ?>
<?php if(isset($form['vendeur_frais_annexes']) && isset($form['acheteur_primes_diverses'])): ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 212px;">Frais et primes</th>
		</tr>
	</thead>
	<tbody>
		<?php
            if(isset($form['vendeur_frais_annexes'])):
        ?>
		<tr>
			<td>
				<?php echo $form['vendeur_frais_annexes']->renderLabel() ?>
			</td>
			<td width="465">
			<?php echo $form['vendeur_frais_annexes']->render(array('rows' => '2', 'cols' => '61')) ?>
			</td>
            <td>
                <a class="btn_minus action_aidesaisie aideSaisieFraisPopup" href="">Ajouter des frais</a>
            </td>
		</tr>
		<?php endif; ?>
		<?php
            if(isset($form['acheteur_primes_diverses'])):
         ?>
		<tr class="alt">
			<td>
				<?php echo $form['acheteur_primes_diverses']->renderLabel() ?>
			</td>
			<td width="465">
			<?php echo $form['acheteur_primes_diverses']->render(array('rows' => '2', 'cols' => '61')) ?>
			</td>
            <td>
                <a class="btn_minus action_aidesaisie aideSaisiePrimesPopup" href="">Ajouter des primes</a>
            </td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
<?php endif; ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th style="width: 212px;">Clauses</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<?php echo $form['conditions_paiement']->renderLabel() ?>
			</td>
			<td width="465">
				<span><?php echo $form['conditions_paiement']->renderError() ?></span>
				<?php echo $form['conditions_paiement']->render(array('class' => 'input_long')) ?>
			</td>
			<td>
				<a class="btn_minus action_aidesaisie aideSaisieDelaiPaiementPopup" href="">Choisir un délai de paiement</a>
			</td>
		</tr>
		<tr class="alt">
			<td>
				<?php echo $form['conditions_particulieres']->renderLabel() ?>
			</td>
			<td width="465">
				<span><?php echo $form['conditions_particulieres']->renderError() ?></span>
				<?php echo $form['conditions_particulieres']->render(array('class' => 'input_long')) ?>
			</td>
			<td></td>
		</tr>
		<?php
            if(isset($form['clause_resiliation'])):
        ?>
		<tr>
			<td>
				<?php echo $form['clause_resiliation']->renderLabel() ?>
			</td>
			<td width="465">
			<?php echo $form['clause_resiliation']->render(array('rows' => '2', 'cols' => '61')) ?>
			</td>
            <td>
                <a class="btn_minus action_aidesaisie aideSaisieResiliationPopup" href="">Saisir les modalités</a>
            </td>
		</tr>
		<?php endif; ?>
		<?php if(isset($form['clause_reserve_propriete'])): ?>
		<tr class="alt">
			<td>
				<?php echo $form['clause_reserve_propriete']->renderLabel() ?>
			</td>
			<td colspan="2">
            <?php echo $form['clause_reserve_propriete']->render() ?>
			<small style="font-size: 12px; color: #666; margin-left: 10px;">(Les modalités de cette clause sont indiquées au <a href="<?php echo url_for('vrac_pdf_annexe', array("type_contrat" => $vrac->type_contrat, "clause_reserve_propriete" => true)) ?>">verso du contrat</a>)</small>
			</td>
		</tr>
		<?php endif; ?>
		<?php if(isset($form['clause_mandat_facturation'])): ?>
		<tr>
			<td>
				<?php echo $form['clause_mandat_facturation']->renderLabel() ?>
			</td>
			<td colspan="2">
			 <small style="font-size: 12px; color: #666;">Le vendeur donne</small> <?php echo $form['clause_mandat_facturation']->render() ?> <small style="font-size: 12px; color: #666; margin-left: 10px;">mandat à l'acheteur ou au représentant du vendeur mandaté d'établir en son nom et pour son compte, les bordereaux récapitulatifs de règlement ou factures suivant les modalités convenues entre les parties dans le mandat.</small>
			</td>
		</tr>
		<?php endif; ?>
		<?php if(isset($form['clause_evolution_prix'])): ?>
		<tr class="alt">
			<td>
				<?php echo $form['clause_evolution_prix']->renderLabel() ?>
			</td>
            <td width="465">
			<?php echo $form['clause_evolution_prix']->render(array('rows' => '2', 'cols' => '61', 'readonly' => 'readonly')) ?>
			</td>
            <td>
                <a class="btn_minus action_aidesaisie aideSaisieEvolutionsPrixPopup" href="">Ajouter des indicateurs</a>
            </td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
<?php endif; ?>
<?php include_partial('vrac/popupAideSaisieFrais', array('target' => $form['vendeur_frais_annexes']->renderId())); ?>
<?php include_partial('vrac/popupAideSaisiePrimes', array('target' => $form['acheteur_primes_diverses']->renderId())); ?>
<?php include_partial('vrac/popupAideSaisieDelaiPaiement', array('target' => $form['conditions_paiement']->renderId(), 'isPluriannuelCadre' => $form->getObject()->isPluriannuelCadre())); ?>
<?php include_partial('vrac/popupAideSaisieResiliation', array('target' => $form['clause_resiliation']->renderId())); ?>
<?php if(isset($form['clause_evolution_prix'])): ?>
<?php include_partial('vrac/popupAideSaisieEvolutionsPrix', array('target' => $form['clause_evolution_prix']->renderId())); ?>
<?php endif; ?>
