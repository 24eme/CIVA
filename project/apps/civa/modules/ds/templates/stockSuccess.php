<?php
use_helper('Float');
use_helper('ds');
?>
<form class="ajaxForm" id="form_stock" action="<?php echo url_for('ds_edition_operateur', $lieu); ?>" method="post" tabindex="0">
	<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3)); ?>
    <?php
        echo $form->renderHiddenFields();
        echo $form->renderGlobalErrors();
    ?>
    <h2 class="titre_page"><?php echo getTitleLieuStockageStock($ds); ?></h2>

	<?php include_partial('ds/onglets', array('ds' => $ds, 'appellation' => $appellation, 'lieu' => $lieu)) ?>

	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">

            <div id="cont_gestion_stock">

			<!-- #gestion_stock -->


			<div id="gestion_stock" class="clearfix gestion_stock_donnees <?php if($lieu->getRawValue() instanceof DSLieu && count($appellation->getConfig()->mentions->getFirst()->lieux) > 1) echo 'avec_sous_onglets'; ?>">
				<?php include_partial('dsEditionFormContentCiva', array('ds' => $ds, 'form' => $form, 'lieu' => $lieu, 'produit_key' => $produit_key));?>
                <?php $tabindex = count($form->getProduitsDetails())*3 + 1 ?>
                <?php if($lieu->getRawValue() instanceof DSLieu && count($appellation->getConfig()->mentions->getFirst()->lieux) > 1):  ?>
			    <div id="sous_total" class="ligne_total">
			        <h3>Sous total lieu-dit</h3>

			        <ul>
			            <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_hors_vt_sgn" /></li>
                                    <?php if($form->hasVTSGN()): ?>
                                        <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_vt" /></li>
                                        <li><input type="text" readonly="readonly" class="somme" data-somme-col="#col_sgn" /></li>
                                    <?php endif; ?>
			        </ul>
			    </div>
               <?php endif; ?>
			</div>
			<!-- fin #gestion_stock -->


			<div id="total" class="ligne_total">
				<h3>Total <?php echo $appellation->libelle; ?></h3>
				<ul>
					<li><input type="text" readonly="readonly" data-val-defaut="<?php echoFloat(getDefaultTotal('total_normal',$appellation, $lieu)); ?>" value="0.00" class="somme" data-somme-col="#col_hors_vt_sgn" /></li>
					<?php if($form->hasVTSGN()): ?>
                                        <li><input type="text" readonly="readonly" data-val-defaut="<?php echoFloat(getDefaultTotal('total_vt',$appellation, $lieu)); ?>" value="0.00" class="somme" data-somme-col="#col_vt" /></li>
					<li><input type="text" readonly="readonly" data-val-defaut="<?php echoFloat(getDefaultTotal('total_sgn',$appellation, $lieu)); ?>" value="0.00" class="somme" data-somme-col="#col_sgn" /></li>
                                        <?php endif; ?>
                                </ul>
			</div>

			<ul id="btn_appelation" class="btn_prev_suiv clearfix">
				<li>
                                <?php if(!$isFirstAppellation): ?>
                                    <a tabindex="<?php echo $tabindex + 1 ?>" class="ajax" href="<?php echo url_for('ds_edition_operateur', $ds->getPreviousLieu($lieu->getRawValue())); ?>">
                                            <img src="/images/boutons/btn_appelation_prec.png" alt="Retourner à l'étape précédente"/>
                                    </a>
                                <?php endif; ?>
				</li>

				<li>
                                    <input tabindex="<?php echo $tabindex ?>" type="image" src="/images/boutons/btn_appelation_suiv.png" alt="Valider et passer à l'appellation suivante" id="valide_form" />
				</li>
			</ul>
		</div>

	</div>
	<!-- fin #application_ds -->

	<ul id="btn_etape" class="btn_prev_suiv clearfix">
		<li class="prec">
			<a class="ajax" href="<?php echo (!$ds->isFirstDs())?
                                                url_for('ds_recapitulatif_lieu_stockage', DSCivaClient::getInstance()->getPreviousDS($ds))
                                              : url_for('ds_lieux_stockage', array('id' => DSCivaClient::getInstance()->getDSPrincipaleByDs($ds)->_id));?>">
				<img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente"  />
			</a>
		</li>
			<li class="suiv">
			<a class="ajax" href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id)); ?>">
				<img src="/images/boutons/btn_passer_etape_suiv.png" alt="Continuer à l'étape suivante"  />
			</a>
		</li>
	</ul>
</form>
