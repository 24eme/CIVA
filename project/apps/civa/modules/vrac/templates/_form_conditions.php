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
<p class="intro_contrat_vrac">Veuillez saisir ici les <strong>conditions applicables</strong> au contrat.</p>

<?php if(isset($form['produits_retiraisons'])): ?>
<table class="validation table_donnees">
	<thead>
		<tr>
			<th>Produits</th>
			<th class="date_retiraison" style="text-align: center"><?php echo $header?> début retiraison</th>
			<th class="date_retiraison" style="text-align: center"><?php echo $header?> limite retiraison</th>
            <th  style="width: 60px">Dupliquer</th>
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
            <td style="text-align: center;">
                <button <?php if($counter >= count($form['produits_retiraisons']) - 1): ?>disabled="disabled" style="opacity: 0.3;"<?php endif; ?> type="button" title="Dupliquer les dates sur la ligne du dessous"  class="btn_majeur btn_petit btn_copy"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-down-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.364 12.5a.5.5 0 0 0 .5.5H14.5a1.5 1.5 0 0 0 1.5-1.5v-10A1.5 1.5 0 0 0 14.5 0h-10A1.5 1.5 0 0 0 3 1.5v6.636a.5.5 0 1 0 1 0V1.5a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v10a.5.5 0 0 1-.5.5H7.864a.5.5 0 0 0-.5.5z"/><path fill-rule="evenodd" d="M0 15.5a.5.5 0 0 0 .5.5h5a.5.5 0 0 0 0-1H1.707l8.147-8.146a.5.5 0 0 0-.708-.708L1 14.293V10.5a.5.5 0 0 0-1 0v5z"/></svg></button>
            </td>
		</tr>
		<?php
			$counter++;
			endforeach;
		?>

	</tbody>
</table>

<script>
    document.querySelectorAll('.btn_copy').forEach(function(btnCopy) {
        btnCopy.addEventListener('click', function() {
            let ligne = this.parentNode.parentNode;
            let ligneSuivante = ligne.nextSibling.nextSibling;
            if(!ligneSuivante) {
                return;
            }
            ligneSuivante.querySelector("input[name*='debut']").value = ligne.querySelector("input[name*='debut']").value;
            ligneSuivante.querySelector("input[name*='limite']").value = ligne.querySelector("input[name*='limite']").value;
        });
    });
</script>
<?php endif; ?>

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
		<?php
            if(isset($form['clause_resiliation'])):
        ?>
		<tr class="alt">
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
		<tr>
			<td>
				<?php echo $form['clause_reserve_propriete']->renderLabel() ?>
			</td>
			<td colspan="2">
            <?php echo $form['clause_reserve_propriete']->render(array('required' => 'required')) ?>
			<small style="font-size: 12px; color: #666; margin-left: 10px;">(Les modalités de cette clause sont indiquées au <a href="<?php echo url_for('vrac_pdf_annexe', array("type_contrat" => $vrac->type_contrat, "clause_reserve_propriete" => true)) ?>">verso du contrat</a>)</small>
			</td>
		</tr>
		<?php endif; ?>
		<?php if(isset($form['clause_mandat_facturation'])): ?>
		<tr class="alt">
			<td>
				<?php echo $form['clause_mandat_facturation']->renderLabel() ?>
			</td>
			<td colspan="2">
			 <small style="font-size: 12px; color: #666;">Le vendeur donne</small> <?php echo $form['clause_mandat_facturation']->render(array('required' => 'required')) ?> <small style="font-size: 12px; color: #666; margin-left: 10px;">mandat à l'acheteur ou au représentant du vendeur mandaté d'établir en son nom et pour son compte, les bordereaux récapitulatifs de règlement ou factures suivant les modalités convenues entre les parties dans le mandat.</small>
			</td>
		</tr>
		<?php endif; ?>
		<?php if(isset($form['clause_evolution_prix'])): ?>
		<tr>
			<td>
				<?php echo $form['clause_evolution_prix']->renderLabel() ?>
			</td>
            <td width="465">
			<?php echo $form['clause_evolution_prix']->render(array('rows' => '2', 'cols' => '61', 'readonly' => 'readonly')) ?>
            <p style="display: <?php echo ($form->getObject()->getPourcentageTotalDesClausesEvolutionPrix() > 0 && $form->getObject()->clause_evolution_prix)? 'block' : 'none'; ?>;">
                Part totale : <span id="partTotale"><?php echo $form->getObject()->getPourcentageTotalDesClausesEvolutionPrix() ?></span>/100
            </p>
			</td>
            <td>
                <a class="btn_minus action_aidesaisie aideSaisieEvolutionsPrixPopup" href="">Ajouter des indicateurs</a> / <a class="inputCleaner" data-target="<?php echo $form['clause_evolution_prix']->renderId() ?>" href="">[x] Vider</a>
            </td>
		</tr>
		<?php endif; ?>
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
        <?php if(isset($form['suivi_qualitatif'])): ?>
		<tr>
			<td>
				<?php echo $form['suivi_qualitatif']->renderLabel() ?>
			</td>
			<td colspan="2">
            <?php echo $form['suivi_qualitatif']->render() ?>
            <small style="font-size: 12px; color: #666; margin-left: 10px;">Sans suivi qualitatif, la date limite de retiraison ne doit pas dépasser 60 jours</small>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>
</table>
<?php endif; ?>
<?php include_partial('vrac/popupAideSaisieFrais', array('target' => $form['vendeur_frais_annexes']->renderId())); ?>
<?php include_partial('vrac/popupAideSaisiePrimes', array('target' => $form['acheteur_primes_diverses']->renderId())); ?>
<?php include_partial('vrac/popupAideSaisieDelaiPaiement', array('target' => $form['conditions_paiement']->renderId(), 'vrac' => $form->getObject())); ?>
<?php include_partial('vrac/popupAideSaisieResiliation', array('target' => $form['clause_resiliation']->renderId())); ?>
<?php if(isset($form['clause_evolution_prix'])): ?>
<?php include_partial('vrac/popupAideSaisieEvolutionsPrix', array('target' => $form['clause_evolution_prix']->renderId())); ?>
<?php endif; ?>
