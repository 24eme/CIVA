<?php use_helper('TemplatingPDF') ?>
<?php use_helper('Float') ?>
<?php use_helper('Date') ?>
<?php use_helper('vracExport'); ?>
<html class="no-js">
	<head>
		<style>
			<?php echo pdfStyle(); ?>
            .th-conditions {
                border-bottom: 0.5px solid #eee; text-align: left; width: 230px; font-weight: bold;
            }
            .td-conditions {
                border-bottom: 0.5px solid #eee; width: 408px; text-align: left;
            }
		</style>
	</head>
	<body>
<?php  include_partial("vrac_export/soussignes", array('vrac' => $vrac));  ?>
<br /><small><br/></small>
<span style="background-color: black; color: white; font-weight: bold;">&nbsp;Produits&nbsp;</span><br/>
<?php $widthProduit = 260; ?>
<?php $widthProduit = (!$odg)? $widthProduit : ($widthProduit + 70); ?>
<?php      $nb_ligne = 23;
           $nb_ligne -= (!$odg)? 0 : 2;
?>
<?php $quantiteType = ($vrac->isInModeSurface())? 'surface' : 'volume'; ?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="text-align: right; border-collapse: collapse;">
	<tr>
		<th width="65px" style="font-weight: bold; text-align: center; border: 1px solid black;">AOC
		</th>
		<th width="<?php echo $widthProduit ?>px" style="font-weight: bold; text-align: center; border: 1px solid black;">Produit</th>
		<th width="42px" style="font-weight: bold; text-align: center; border: 1px solid black;">Mill.</th>
		<?php if (!$odg): ?>
		<th width="58px" style="font-weight: bold; text-align: center; border: 1px solid black;">Prix<br/><small>(en <?php echo $vrac->getPrixUniteLibelle(); ?>)</small></th>
		<?php endif; ?>
		<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
		<th width="85px" style="font-weight: bold; text-align: center; border: 1px solid black;">Centilisation</th>
		<th width="70px" style="font-weight: bold; text-align: center; border: 1px solid black;">Nb bouteilles</th>
                <th width="57px" style="font-weight: bold; text-align: center; border: 1px solid black;">Volume expédié<br/><small>(en hl)</small></th>
		<?php else: ?>
		<th width="75px" style="font-weight: bold; text-align: center; border: 1px solid black;"><?php echo ucfirst($quantiteType); ?> <?php if(!$vrac->needRetiraison()): ?>engagé<?php else: ?>estimé<?php endif; ?><?php if ($vrac->isInModeSurface()): ?>e<?php endif; ?><br/><small>(en <?php echo ($vrac->isInModeSurface())? 'ha' : 'hl'; ?>)</small></th>
		<th width="75px" style="font-weight: bold; text-align: center; border: 1px solid black; <?php if (!$vrac->needRetiraison()): ?>background-color: #eaeaea;<?php endif; ?>"><?php if($vrac->needRetiraison()): ?>Volume réel<br/><small>(en hl)</small><?php endif; ?></th>
        <th width="62px" style="font-weight: bold; text-align: center; border: 1px solid black; <?php if (!$vrac->needRetiraison()): ?>background-color: #eaeaea;<?php endif; ?>"><?php if($vrac->needRetiraison()): ?>Date<br/>de Chargt<?php endif; ?></th>
        <?php endif; ?>
	</tr>
	<?php
        $cptDetail = 0;
        foreach ($vrac->declaration->getProduitsDetailsSorted() as $product):
			$productLine = $product->getRawValue();
					foreach ($productLine as $detailKey => $detailLine):
                                            $nb_ligne = $nb_ligne - 1;
							$backgroundColor = getColorRowDetail($detailLine);
			$libelle_produit = $detailLine->getCepage()->getLibelle()." ".$detailLine->getLieuLibelle()." ".$detailLine->getLieuDit()." ".$detailLine->getVtsgn()." ".$detailLine->getDenomination();
			$libelle_produit.= ($detailLine->exist('label') && $detailLine->get("label"))? " ".VracClient::$label_libelles[$detailLine->get("label")] : "";
                        $isOnlyOneRetiraison = $vrac->isCloture() && (count($detailLine->retiraisons) === 1);
                        $dateRetiraison = "";
                        $lastDetail = ((count($vrac->declaration->getProduitsDetailsSorted()) - 1) == $cptDetail);
                        if($isOnlyOneRetiraison){
                            $retiraisons = $detailLine->retiraisons->toArray(true,false);
                            $dateRetiraison = getDateFr($retiraisons[0]['date']);
                        }
                        ?>
	<tr>
			<td class="td-large" width="65px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: center;"><small style="font-size: 1.2pt;"><br /></small><span style="font-size: 6pt;"><?php echo $detailLine->getCepage()->getAppellation()->getCodeCiva(); ?></span></td>
			<td class="td-large" width="<?php echo $widthProduit ?>px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: left; font-size: 8pt;"><small style="font-size: 2.4pt;"><br /></small>&nbsp;<?php echo truncate_text($libelle_produit,70);  ?></td>
			<td class="td-large" width="42px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: center;"><?php echo pdfTdLargeStart() ?><?php echo $detailLine->getMillesime(); ?>&nbsp;</td>
			<?php if (!$odg): ?>
			<td class="td-large" width="58px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echo pdfTdLargeStart() ?><?php echoPrix($detailLine->getPrixUnitaire()); ?></td>
			<?php endif; ?>
			<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
			<td class="td-large" width="85px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echo pdfTdLargeStart() ?><?php echoCentilisation(VracClient::getLibelleCentilisation($detailLine->centilisation)) ?></td>
			<td class="td-large" width="70px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echo pdfTdLargeStart() ?><?php if ($vrac->isCloture()): ?><?php echo $detailLine->nb_bouteille; ?><?php endif; ?>&nbsp;&nbsp;</td>
            <td class="td-large" width="57px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echo pdfTdLargeStart() ?><?php ($vrac->isInModeSurface()? echoSurface($detailLine->surface_propose) : echoVolume($detailLine->volume_propose)); ?></td>
			<?php else: ?>
			<td class="td-large" width="75px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;"><?php echo pdfTdLargeStart() ?><?php ($vrac->isInModeSurface()? echoSurface($detailLine->surface_propose) : echoVolume($detailLine->volume_propose)); ?></td>
			<td class="td-large" width="75px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: right;<?php if (!$vrac->isCloture() || !$vrac->needRetiraison()): ?> background-color: #eaeaea;<?php endif; ?>"><?php echo pdfTdLargeStart() ?><?php if ($vrac->isCloture()): ?><?php echoVolume($detailLine->volume_enleve); ?><?php endif; ?></td>
            <td class="td-large" width="62px" style="border: 1px solid black; <?php echo $backgroundColor ?> text-align: center;<?php if (!$isOnlyOneRetiraison): ?> background-color: #eaeaea;<?php endif; ?><?php if (!$dateRetiraison): ?> font-size: 7pt;<?php endif; ?>"><?php if ($dateRetiraison): ?><?php echo pdfTdLargeStart() ?><?php echo $dateRetiraison; ?><?php endif; ?></td>
			<?php endif; ?>
        </tr>
        <?php
        $cptDetail++;
        if($vrac->isCloture() && (count($detailLine->retiraisons) > 1)):
        $cpt = 0;
        foreach ($detailLine->retiraisons as $retiraison):
            $border_bottom = (((count($detailLine->retiraisons) - 1 ) == $cpt) && $lastDetail)? "border-bottom: 1px solid black; border-bottom: 1px solid black;" : "";
            $nb_ligne = $nb_ligne - 1;
            ?>
                <tr>
                    <td class="td-large" colspan="5" style="border-left: 1px solid black; <?php echo $border_bottom; ?> "><?php echo pdfTdLargeStart() ?></td>
                    <td class="td-large" width="75px" style="border: 1px solid black; text-align: right;"><?php echo pdfTdLargeStart() ?><?php echoVolume($retiraison->volume); ?></td>
                    <td class="td-large" width="62px" style="border: 1px solid black;  text-align: center;"><?php echo pdfTdLargeStart() ?><?php echoDateFr($retiraison->date); ?></td>
                </tr>
        <?php
        $cpt++;
                        endforeach;
                endif;
		endforeach;
        endforeach;
	?>
	<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
	<tr>
			<td class="td-large" style="text-align: left;" colspan="<?php if (!$odg): ?>6<?php else: ?>5<?php endif; ?>" >&nbsp;</td>
            <td class="td-large" style="border: 1px solid black; <?php if (!$vrac->isCloture()): ?> background-color: #eaeaea;<?php endif; ?>"><?php echo pdfTdLargeStart() ?><?php if ($vrac->isCloture()): ?><?php echoVolume($vrac->getTotalVolumeEnleve(),true); ?><?php endif; ?></td>
	</tr>
	<?php else: ?>
	<tr>
			<td class="td-large" style="text-align: left;" colspan="<?php if (!$odg): ?>4<?php else: ?>3<?php endif; ?>" >&nbsp;</td>
			<td class="td-large" style="border: 1px solid black;"><?php echo pdfTdLargeStart() ?><?php ($vrac->isInModeSurface())? echoSurface($vrac->getTotalSurfacePropose(),true) : echoVolume($vrac->getTotalVolumePropose(),true); ?></td>
            <?php if($vrac->needRetiraison()): ?>
            <td class="td-large" style="border: 1px solid black; <?php if (!$vrac->isCloture()): ?> background-color: #eaeaea;<?php endif; ?>"><?php echo pdfTdLargeStart() ?><?php if ($vrac->isCloture()): ?><?php echoVolume($vrac->getTotalVolumeEnleve(),true); ?><?php endif; ?></td>
            <?php endif; ?>
	</tr>
	<?php endif; ?>
</table>

<?php if($nb_ligne < 14): ?>
    <div style="page-break-before:always"></div>
    <?php $nb_ligne = 29; ?>
    <?php include_partial("vrac_export/soussignes", array('vrac' => $vrac));  ?>
<?php endif; ?>

<br />
<small><br /></small>
<?php $nb_ligne -= 1.5 ?>

<span style="background-color: black; color: white; font-weight: bold;">&nbsp;Conditions&nbsp;</span><br/>
<table border="0" cellspacing="0" cellpadding="0" width="100%" style="text-align: right; border-collapse: collapse;">
	<tr>
        <th class="td-large th-conditions" style="border-top: 0.5px solid #eee; "><?php echo pdfTdLargeStart() ?>Frais annexes à la charge du vendeur</th>
        <td class="td-large td-conditions" style="border-top: 0.5px solid #eee; "><?php echo pdfTdLargeStart() ?>&nbsp;<?php echo ($vrac->exist('vendeur_frais_annexes') && $vrac->vendeur_frais_annexes) ? str_replace("\n", '<br />&nbsp;', $vrac->vendeur_frais_annexes) : "Aucun" ?></td>
    </tr>
        <?php $nb_ligne -= ($vrac->exist('vendeur_frais_annexes'))? (count(explode("\n", $vrac->vendeur_frais_annexes))) : 1; ?>
    <tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>CVO à la charge du vendeur</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php echo $vrac->getTauxCvo(); ?> € HT/hl</td>
    </tr>
    <?php $nb_ligne -= 1 ?>
    <tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>Primes diverses à la charge de l’acheteur</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php echo ($vrac->exist('acheteur_primes_diverses') && $vrac->acheteur_primes_diverses) ? str_replace("\n", '<br />&nbsp;', $vrac->acheteur_primes_diverses) : "Aucune" ?></td>
    </tr>
    <?php $nb_ligne -= ($vrac->exist('acheteur_primes_diverses'))? (count(explode("\n", $vrac->acheteur_primes_diverses))) : 1; ?>
	<?php if($vrac->isPluriannuelCadre()):  ?>
	<tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>Critères et modalités d’évolution des prix</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php echo ($vrac->exist('clause_evolution_prix') && $vrac->clause_evolution_prix) ? str_replace("\n", '<br />&nbsp;', $vrac->clause_evolution_prix) : "Aucune" ?>
		<br /><small><i>&nbsp;Les indicateurs ainsi que la méthode de calcul du prix, basé sur ces indicateurs resteront les mêmes sur l’ensemble de la période contractualisée (Année N, N+1 et N+2).</i></small></td>
    </tr>
    <?php $nb_ligne -= 1.5 + (count(explode("\n", $vrac->clause_evolution_prix))); ?>
	<?php endif; ?>
    <tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>Délais de paiement</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php echo ($vrac->conditions_paiement)? str_replace("\n", '<br />&nbsp;', $vrac->conditions_paiement) : 'Aucun' ?></td>
    </tr>
    <?php $nb_ligne -= 1.5 ?>
    <tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>Résiliation hors cas de force majeur</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php echo ($vrac->exist('clause_resiliation') && $vrac->clause_resiliation) ? str_replace("\n", '<br />&nbsp;', $vrac->clause_resiliation) : "Aucune" ?><small><br /><i>&nbsp;La résiliation est signifiée par la partie demanderesse par lettre recommandée avec AR.</i></small></td>
    </tr>
    <?php $nb_ligne -= ($vrac->exist('clause_resiliation'))? 1.5 + (count(explode("\n", $vrac->clause_resiliation))) : 1.5; ?>
    <?php if ($vrac->exist('clause_reserve_propriete')): ?>
    <tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>Clause de réserve de propriété</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php if($vrac->clause_reserve_propriete): ?><strong>Oui</strong><?php else: ?>Oui<?php endif; ?> <span style="font-family: Dejavusans"><?php if($vrac->clause_reserve_propriete): ?>☑<?php else: ?>☐<?php endif; ?></span>&nbsp;&nbsp;&nbsp;<?php if(!$vrac->clause_reserve_propriete): ?><strong>Non</strong><?php else: ?>Non<?php endif; ?> <span style="font-family: Dejavusans"><?php if(!$vrac->clause_reserve_propriete): ?>☑<?php else: ?>☐<?php endif; ?></span><small><i>&nbsp;&nbsp;&nbsp;Les modalités sont indiquées au verso de ce formulaire</i></small></td>
    </tr>
    <?php $nb_ligne -= 1 ?>
    <?php endif; ?>
    <?php if ($vrac->exist('clause_mandat_facturation')): ?>
    <tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>Mandat de facturation</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php if($vrac->clause_mandat_facturation): ?><strong>Oui</strong><?php else: ?>Oui<?php endif; ?> <span style="font-family: Dejavusans"><?php if($vrac->clause_mandat_facturation): ?>☑<?php else: ?>☐<?php endif; ?></span>&nbsp;&nbsp;&nbsp;<?php if(!$vrac->clause_mandat_facturation): ?><strong>Non</strong><?php else: ?>Non<?php endif; ?> <span style="font-family: Dejavusans"><?php if(!$vrac->clause_mandat_facturation): ?>☑<?php else: ?>☐<?php endif; ?></span><small><i>&nbsp;&nbsp;&nbsp;Le vendeur donne mandat à l’acheteur d’établir en son nom et pour son compte, les bordereaux<br />&nbsp;récapitulatifs de règlement ou factures suivant les modalités convenues entre les parties dans le mandat.</i></small></td>
    </tr>
    <?php $nb_ligne -= 1.5 ?>
    <?php endif; ?>
	<?php if($vrac->exist('suivi_qualitatif')): ?>
	<tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>Suivi qualitatif</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php if($vrac->suivi_qualitatif): ?><strong>Oui</strong><?php else: ?>Oui<?php endif; ?> <span style="font-family: Dejavusans"><?php if($vrac->suivi_qualitatif): ?>☑<?php else: ?>☐<?php endif; ?></span>&nbsp;&nbsp;&nbsp;<?php if(!$vrac->suivi_qualitatif): ?><strong>Non</strong><?php else: ?>Non<?php endif; ?> <span style="font-family: Dejavusans"><?php if(!$vrac->suivi_qualitatif): ?>☑<?php else: ?>☐<?php endif; ?></span></td>
    </tr>
    <?php $nb_ligne -= 1 ?>
	<?php endif; ?>
    <?php if($vrac->exist('delais_retiraison')): ?>
    <tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>Délai maximum de retiraison</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php echo ($delais = $vrac->getDelaisRetiraison())? $delais : VracClient::DELAIS_RETIRAISON_AUCUN; ?></td>
    </tr>
    <?php $nb_ligne -= 1 ?>
    <?php endif; ?>
    <tr>
        <th class="td-large th-conditions"><?php echo pdfTdLargeStart() ?>Autres clauses particulières</th>
        <td class="td-large td-conditions"><?php echo pdfTdLargeStart() ?>&nbsp;<?php echo ($vrac->conditions_particulieres) ? str_replace("\n", '<br />&nbsp;', $vrac->conditions_particulieres) : "Aucune" ?></td>
    </tr>
    <?php $nb_ligne -= 1 ?>
</table>

<?php if($vrac->hasAnnexes()): ?>
<br /><small><br />&nbsp;<br />&nbsp;</small>
<span style="background-color: black; color: white; font-weight: bold;">&nbsp;Annexes&nbsp;</span><br/>
<span>&nbsp;&nbsp;Des annexes ont été jointes à ce contrat, elles sont consultables dans la version dématérialisée sur le portail du CIVA.</span>
<?php $nb_ligne -= 2 ?>
<?php endif; ?>

<?php for($i=0;$i<$nb_ligne;$i++): ?>
<small><br />&nbsp;<br />&nbsp;</small>
<?php endfor;?>

<span>&nbsp;</span><br />
<?php if($vrac->hasCourtier()) {$widthSignataire = 33.33;} else {$widthSignataire = 50; } ?>
<table cellspacing="0" cellpadding="0" border="0" width="100%" style="text-align: left; border-collapse: collapse;">
	<tr>
		<td width="<?php echo $widthSignataire ?>%" valign="top" style="border: 1px solid #000;">
			<table cellspacing="0" cellpadding="5" border="0" width="100%">
				<tr>
					<th style="background-color: grey; text-align: center; color: #FFF; font-weight: bold;">LE VENDEUR</th>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_vendeur): ?>le <?php echo preg_replace('/^(\d+)\-(\d+)\-(\d+)$/', '\3/\2/\1', $vrac->valide->date_validation_vendeur); ?>,<?php endif; ?></td>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_vendeur): ?>signé éléctroniquement<?php endif; ?></td>
				</tr>
			</table>
		</td>
		<?php if($vrac->hasCourtier()): ?>
		<td width="<?php echo $widthSignataire ?>%" valign="top" style="border: 1px solid #000;">
			<table cellspacing="0" cellpadding="5" border="0" width="100%">
				<tr>
					<th style="background-color: grey; text-align: center; color: #FFF; font-weight: bold;">VU, le Courtier</th>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_mandataire): ?>le <?php echo preg_replace('/^(\d+)\-(\d+)\-(\d+)$/', '\3/\2/\1', $vrac->valide->date_validation_mandataire); ?>,<?php endif; ?></td>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_mandataire): ?>signé éléctroniquement<?php endif; ?></td>
				</tr>
			</table>
		</td>
		<?php endif; ?>
		<td width="<?php echo $widthSignataire ?>%" valign="top" style="border: 1px solid #000;">
			<table cellspacing="0" cellpadding="5" border="0" width="100%">
				<tr>
					<th style="background-color: grey; text-align: center; color: #FFF; font-weight: bold;">L'ACHETEUR</th>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_acheteur): ?>le <?php echo preg_replace('/^(\d+)\-(\d+)\-(\d+)$/', '\3/\2/\1', $vrac->valide->date_validation_acheteur); ?>,<?php endif; ?></td>
				</tr>
				<tr>
					<td><?php if ($vrac->valide->date_validation_acheteur): ?>signé éléctroniquement<?php endif; ?></td>
				</tr>
			</table>
		</td>
	</tr>

</table>
<table cellspacing="0" cellpadding="0" border="0" style="text-align: right; margin:0; padding: 0;">
	<tr>
		<td style="text-align: left; font-size: 6pt;"><?php echo getLastSentence(); ?></td>
    </tr>
</table>
</body>
</html>
