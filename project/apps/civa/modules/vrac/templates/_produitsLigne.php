<?php include_partial('vrac/produitsProduit', array('detail' => $detail, 'produits_hash_in_error' => isset($produits_hash_in_error) ? $produits_hash_in_error : null)) ?>
<?php if ($vrac->type_contrat == VracClient::TYPE_BOUTEILLE): ?>
	<?php include_partial('vrac/produitsNombreBouteilles', array('detail' => $detail)) ?>
	<?php include_partial('vrac/produitsCentilisation', array('detail' => $detail)) ?>
	<?php include_partial('vrac/produitsPrix', array('vrac' => $vrac, 'detail' => $detail)) ?>
	<?php if ($vrac->needRetiraison() && ($vrac->isCloture() || $form)): ?>
		<?php include_partial('vrac/produitsVolumeEnleve', array('vrac' => $vrac, 'detail' => $detail, 'quantiteType' => $quantiteType)) ?>
	<?php else :  ?>
		<?php include_partial('vrac/produitsVolumePropose', array('vrac' => $vrac, 'detail' => $detail, 'quantiteType' => $quantiteType)) ?>
	<?php endif; ?>
<?php else: ?>
	<?php include_partial('vrac/produitsVolumePropose', array('vrac' => $vrac, 'detail' => $detail, 'quantiteType' => $quantiteType)) ?>
	<?php include_partial('vrac/produitsPrix', array('vrac' => $vrac, 'detail' => $detail)) ?>
    <?php if($form): ?>
        <td class="echeance"></td>
        <td class="enleve"><strong id="vol<?php echo renderProduitIdentifiant($detail) ?>" data-compare="prop<?php echo renderProduitIdentifiant($detail) ?>" data-cibling="<?php echo $formProduit['cloture']->renderId() ?>"><?php echo echoFloat($detail->volume_enleve) ?></strong> hl</td>
        <td class="cloture">
            <input type="checkbox" name="<?php echo $formProduit['cloture']->renderName(); ?>" id="<?php echo $formProduit['cloture']->renderId(); ?>" value="<?php echo "1"; ?>" <?php echo ($detail->cloture)? "checked='checked'" : '' ?>  <?php echo ($detail->exist('volume_enleve') && $detail->volume_enleve !== null)? '' : "style='display:none'"; ?> />
        </td>
        <td>
            <?php if (!$detail->cloture): ?>
            <a class="btn_ajouter_ligne_template" data-container-last-brother=".produits" data-template="#template_form_<?php echo str_replace('/', '_', $key); ?>_retiraisons_item" href="#">Enlever</a>
            <script id="template_form_<?php echo str_replace('/', '_', $key); ?>_retiraisons_item" class="template_form" type="text/x-jquery-tmpl">
                    <?php echo include_partial('form_retiraisons_item', array('detail' => $detail, 'form' => $form->getFormTemplateRetiraisons($detail->getRawValue(), $key))); ?>
            </script>
            <?php endif; ?>
        </td>
    <?php elseif ($vrac->needRetiraison() && ($vrac->isCloture() || $form)): ?>
		<td></td>
		<?php include_partial('vrac/produitsVolumeEnleve', array('vrac' => $vrac, 'detail' => $detail, 'quantiteType' => $quantiteType)) ?>
    <?php elseif($vrac->needDateRetiraison()): ?>
        <td class="date_retiraison_limite <?php echo isVersionnerCssClass($detail, 'retiraison_date_debut') ?>" style="text-align: center;">
            <?php if($detail->retiraison_date_debut && !$vrac->isPluriannuelCadre()): ?>
            <?php echo format_date($detail->retiraison_date_debut, 'dd/MM/yyyy') ?>
            <?php else: ?>
                <?php echo format_date('1970-'.$detail->retiraison_date_debut, 'dd/MM') ?>
            <?php endif; ?>
        </td>
        <td class="date_retiraison_limite <?php echo isVersionnerCssClass($detail, 'retiraison_date_limite') ?>" style="text-align: center;">
            <?php if($detail->retiraison_date_limite && !$vrac->isPluriannuelCadre()): ?>
                <?php echo format_date($detail->retiraison_date_limite, 'dd/MM/yyyy') ?>
            <?php else: ?>
                <?php echo format_date('1970-'.$detail->retiraison_date_limite, 'dd/MM') ?>
            <?php endif;  ?>
        </td>
    <?php else: ?>
        <td colspan="2"></td>
	<?php endif; ?>
<?php endif; ?>
