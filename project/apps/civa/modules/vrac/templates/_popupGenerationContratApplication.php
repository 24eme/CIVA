<div id="popup_generation_contratApplication" data-size-popup="900" style="display:none;" title="Générer le contrat d'application <?php echo $form->getObject()->campagne; ?>">
    <form id="contrats_vrac" class="ui-tabs" method="post" action="<?php echo url_for('vrac_generer_contrat_application', array('numero_contrat' => substr($form->getObject()->numero_contrat, 0, -4), 'campagne' => substr($form->getObject()->campagne, 0, 4))); ?>">
        <div class="fond">
            <?php echo $form->renderHiddenFields() ?>
            <?php echo $form->renderGlobalErrors() ?>

            <p class="intro_contrat_vrac">Veuillez valider les volumes et prix pour l'ensemble des produits.</p>
            <table class="etape_produits produits table_donnees">
            	<thead>
            		<tr>
            			<th class="produit">Produits</th>
            			<th class="volume"><span>Volume estimé</span></th>
            			<th class="prix"><span>Prix</span></th>
                        <?php if (!$form->getObject()->isPremiereApplication()): ?>
            			<th class="volume"><span>Volume</span></th>
            			<th class="prix"><span>Prix</span></th>
                        <?php endif; ?>
            		</tr>
            	</thead>
            	<tbody>
            	<?php
            		$counter = 0;
            		foreach ($form['produits'] as $key => $embedForm) :
            			$detail = $form->getObject()->get($key);
            			$alt = ($counter%2);
            	?>
            		<tr<?php if ($alt): ?> class="alt"<?php endif; ?>>
            			<td class="produit">
                		    <?php echo $detail->getLibelleSansCepage(); ?>
                            <strong><?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?>  <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong><?php echo ($detail->exist('label') && $detail->get("label"))? " ".VracClient::$label_libelles[$detail->get("label")] : ""; ?>
                        </td>
            			<td class="volume">
        				    <?php echo $detail->volume_propose; ?>&nbsp;hl
            			</td>
            			<td class="prix">
            				<?php echo $detail->prix_unitaire; ?>&nbsp;&euro;/hl
            			</td>
                        <?php if (!$form->getObject()->isPremiereApplication()): ?>
            			<td class="volume">
        				    <span><?php echo $embedForm['volume_propose']->renderError() ?></span>
        				    <?php echo $embedForm['volume_propose']->render(array('class' => 'num', 'required' => 'required', 'value' => null)) ?>&nbsp;hl
            			</td>
            			<td class="prix">
            				<span><?php echo $embedForm['prix_unitaire']->renderError() ?></span>
            				<?php echo $embedForm['prix_unitaire']->render(array('class' => 'num', 'required' => 'required', 'value' => null)) ?>&nbsp;&euro;/hl
            			</td>
                        <?php endif; ?>
            		</tr>
            	<?php $counter++; endforeach; ?>
            	</tbody>
            </table>
        </div>

        <div id="btns" class="clearfix" style="text-align: center; margin-top: 8px;">
            <input type="image" src="/images/boutons/btn_valider.png" alt="Générer le contrat" name="boutons[next]" id="genereContratApplication_OK" class="valideDS_OK" />
            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
        </div>
    </form>
</div>
