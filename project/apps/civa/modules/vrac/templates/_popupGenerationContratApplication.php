<?php use_helper('Date') ?>
<?php $quantiteType = ($form->getObject()->isInModeSurface())? 'surface' : 'volume'; ?>
<div id="<?php echo $id ?>" class="popup_ajout" data-size-popup="900" style="display:none;" title="Générer le contrat d'application <?php echo $form->getObject()->campagne; ?>">
    <form id="contrats_vrac" class="ui-tabs" method="post" action="<?php echo ($form->getObject()->hasDoubleValidation() && $form->getObject()->needSaisiePrix())? url_for('vrac_revalidation_contrat_application', $form->getObject()) : url_for('vrac_generer_contrat_application', array('numero_contrat' => substr($form->getObject()->numero_contrat, 0, -4), 'campagne' => substr($form->getObject()->campagne, 0, 4))); ?>">
        <?php
            if($validation && $validation->hasPoints()) {
                include_partial('global/validation', array('validation' => $validation, 'afficheLiens' => false));
            }
        ?>
        <div class="fond">
            <?php echo $form->renderHiddenFields() ?>
            <?php echo $form->renderGlobalErrors() ?>

            <p class="intro_contrat_vrac">Veuillez valider les volumes et prix pour l'ensemble des produits.</p>
            <table class="validation produits table_donnees">
            	<thead>
            		<tr>
            			<th class="produit">Produits</th>
            			<th class="volume"><?php if ($form->getObject()->getContratPluriannuelCadre() && $form->getObject()->getContratPluriannuelCadre()->contrat_pluriannuel_mode_surface): ?>Surface engagée<?php else: ?>Volume estimé<?php endif; ?></th>
                        <?php if($vrac->type_contrat == VracClient::TYPE_VRAC && $form->getObject()->isPremiereApplication()): ?>
                        <th class="volume">Dont volume<br />en réserve</th>
                        <?php endif; ?>
            			<th class="prix">Prix</th>
                        <?php if (!$form->getObject()->isPremiereApplication()): ?>
            			<th class="volume"><?php echo ucfirst($quantiteType); ?></th>
                        <?php if($vrac->type_contrat == VracClient::TYPE_VRAC && !$form->getObject()->isPremiereApplication()): ?>
                        <th class="volume">Dont volume<br />en réserve</th>
                        <?php endif; ?>
            			<th class="prix">Prix</th>
                        <?php elseif ($form->getObject()->getContratPluriannuelCadre() && $form->getObject()->getContratPluriannuelCadre()->contrat_pluriannuel_mode_surface && $form->getObject()->getContratPluriannuelCadre()->type_contrat != VracClient::TYPE_RAISIN): ?>
            			<th class="volume">Volume</th>
                        <?php if($vrac->type_contrat == VracClient::TYPE_VRAC && !$form->getObject()->isPremiereApplication()): ?>
                        <th class="volume">Dont volume<br />bloqué</th>
                        <?php endif; ?>
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
                            <?php if ($form->getObject()->getContratPluriannuelCadre() && $form->getObject()->getContratPluriannuelCadre()->contrat_pluriannuel_mode_surface): ?>
                                <?php echo $detail->surface_propose; ?>&nbsp;ares
                            <?php else: ?>
                                <?php echo $detail->volume_propose; ?>&nbsp;hl
                            <?php endif; ?>
            			</td>
                        <?php if($vrac->type_contrat == VracClient::TYPE_VRAC && $form->getObject()->isPremiereApplication()): ?>
                        <td class="volume">
                            <?php if(isset($embedForm['dont_volume_bloque'])): ?>
            				<span><?php echo $embedForm['dont_volume_bloque']->renderError() ?></span>
            				<?php echo $embedForm['dont_volume_bloque']->render(array('class' => 'num', 'style' => 'width:58px;')) ?>&nbsp;hl
                            <?php endif; ?>
            			</td>
                        <?php endif; ?>
            			<td class="prix">
            				<?php echo $detail->prix_unitaire; ?>&nbsp;<?php echo $form->getObject()->getPrixUniteLibelle(); ?>
            			</td>
                        <?php if (!$form->getObject()->isPremiereApplication()): ?>
            			<td class="volume">
                            <?php $attr = ($form->getObject()->hasDoubleValidation() && $form->getObject()->needSaisiePrix())? ['class' => 'num', 'readonly' => 'readonly', 'required' => 'required', 'style' => 'visibility: hidden; width:58px;'] : ['class' => 'num', 'required' => 'required', 'style' => 'width:58px;'] ?>
        				    <span><?php echo $embedForm[$quantiteType.'_propose']->renderError() ?></span>
        				    <?php echo $embedForm[$quantiteType.'_propose']->render($attr) ?>&nbsp;<?php if (!$form->getObject()->hasDoubleValidation()||!$form->getObject()->needSaisiePrix()): ?><?php echo ($quantiteType == 'surface')? 'ares' : 'hl'; ?><?php endif; ?>
                        </td>
                        <?php if($vrac->type_contrat == VracClient::TYPE_VRAC): ?>
                        <td class="volume">
                            <?php if(isset($embedForm['dont_volume_bloque'])): ?>
                            <span><?php echo $embedForm['dont_volume_bloque']->renderError() ?></span>
                            <?php echo $embedForm['dont_volume_bloque']->render(array('class' => 'num', 'style' => 'width:58px;')) ?>&nbsp;hl
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
            			<td class="prix">
            				<span><?php echo $embedForm['prix_unitaire']->renderError() ?></span>
                            <?php $attr = ($form->getObject()->hasDoubleValidation() && !$form->getObject()->needSaisiePrix())? ['class' => 'num', 'style' => 'width:58px;'] : ['class' => 'num', 'required' => 'required', 'style' => 'width:58px;']; ?>
            				<?php echo $embedForm['prix_unitaire']->render($attr) ?>&nbsp;<?php echo $form->getObject()->getPrixUniteLibelle(); ?>
            			</td>
                    <?php elseif ($form->getObject()->getContratPluriannuelCadre() && $form->getObject()->getContratPluriannuelCadre()->contrat_pluriannuel_mode_surface && $form->getObject()->getContratPluriannuelCadre()->type_contrat != VracClient::TYPE_RAISIN): ?>
            			<td class="volume">
        				    <span><?php echo $embedForm['volume_propose']->renderError() ?></span>
        				    <?php echo $embedForm['volume_propose']->render(array('class' => 'num', 'required' => 'required', 'style' => 'width:58px;')) ?>&nbsp;hl
            			</td>
                        <?php if($vrac->type_contrat == VracClient::TYPE_VRAC): ?>
                        <td class="volume">
                            <?php if(isset($embedForm['dont_volume_bloque'])): ?>
                            <span><?php echo $embedForm['dont_volume_bloque']->renderError() ?></span>
                            <?php echo $embedForm['dont_volume_bloque']->render(array('class' => 'num', 'style' => 'width:58px;')) ?>&nbsp;hl
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <?php endif; ?>
            		</tr>
            	<?php $counter++; endforeach; ?>
            	</tbody>
            </table>
        </div>

        <div id="btns" class="clearfix" style="text-align: center; margin-top: 8px;">
            <?php if(!$validation||!$validation->hasPoints()): ?>
                <input type="image" src="/images/boutons/btn_valider.png" alt="Générer le contrat" name="boutons[next]" id="genereContratApplication_OK" class="valideDS_OK" />
            <?php endif; ?>
            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
        </div>
    </form>
</div>
