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
        <?php if($vrac->getTauxCvo()) : ?>
        <tr>
			<td>
				CVO à la charge du vendeur
			</td>
			<td colspan="2">
				<?php echo $vrac->getTauxCvo(); ?> € HT/hl
			</td>
		</tr>
        <?php endif; ?>
		<?php endif; ?>

		<?php
            if(isset($form['acheteur_primes_diverses'])):
         ?>
		<tr>
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
		<tr>
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
        <?php if(isset($form['nb_jour_apres_recolte_retiraison'])): ?>
        <tr>
            <td>
                <?php echo $form['nb_jour_apres_recolte_retiraison']->renderLabel() ?>
            </td>
            <td colspan="2">
            <?php echo $form['nb_jour_apres_recolte_retiraison']->render(array('style' => 'width: 100px;')) ?>
            <span>jours après la récolte</span>
            </td>
        </tr>
        <?php endif; ?>
        <?php if($vrac->getDelaisRetiraison()): ?>
        <tr>
            <td>
                <label>Délai maximum de retiraison</label>
            </td>
            <td colspan="2">
            60 jours après la validation du contrat
            </td>
        </tr>
        <?php endif; ?>
		<tr>
			<td>
				<?php echo $form['conditions_particulieres']->renderLabel() ?>
			</td>
			<td width="465">
				<span><?php echo $form['conditions_particulieres']->renderError() ?></span>
				<?php echo $form['conditions_particulieres']->render(array('class' => 'input_long')) ?>
			</td>
			<td></td>
		</tr>
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
