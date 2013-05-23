<form action="" method="post">
    <?php 
        echo $form->renderHiddenFields();
        echo $form->renderGlobalErrors();
        include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3));
    ?>
    <div id="ajax_error"></div>
	<h2 class="titre_page"><?php echo $ds->declarant->cvi.' - '.$ds->getEtablissement()->getNom().' - '.$ds->getEtablissement()->getAdresse(); ?></h2>
	
	<?php include_partial('ds/onglets', array('appellation' => $appellation, 'lieu' => $lieu)) ?>
		
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		<div id="aucun_produit">
            <?php if(count($lieu->getProduitsDetails()) < 1): ?>
                <p>Aucun produit défini pour cette appellation</p>
            <?php else :?>
                <p>Ajouter un produit</p>
            <?php endif; ?>   
			
			<div class="form_ligne">
                <?php echo $form['hashref']->renderLabel(); ?>
                <?php echo $form['hashref']->render(); ?>
                <?php echo $form['hashref']->renderError(); ?>
			</div>

            <?php if($form->hasLieuEditable()): ?>
			<div class="form_ligne">
				<?php echo $form['lieudit']->renderLabel(); ?>
                <?php echo $form['lieudit']->render(); ?>
                <?php echo $form['lieudit']->renderError(); ?>
			</div>
			<?php endif; ?>
			<div class="form_btn">
                <?php if(count($lieu->getProduitsDetails()) > 0): ?>
                    <a href="<?php echo url_for('ds_edition_operateur', $lieu) ?>">Annuler</a>
                <?php endif; ?>
                <input type="image" src="/images/boutons/btn_valider.png" alt="Valider" />
			</div>

		</div>			
	</div>
	<!-- fin #application_ds -->

</form>