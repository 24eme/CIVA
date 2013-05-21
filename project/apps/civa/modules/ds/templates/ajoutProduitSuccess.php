<?php 
$current_lieu = null;
$firstAppellation = ($ds->getFirstAppellationLieu() == $appellation_lieu) && ($ds->isDsPrincipale());
?>
<form action="" method="post">
    <?php 
        echo $form->renderHiddenFields();
        echo $form->renderGlobalErrors();
        include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3));
    ?>
    <div id="ajax_error"></div>
	<h2 class="titre_page"><?php echo $ds->declarant->cvi.' - '.$ds->getEtablissement()->getNom().' - '.$ds->getEtablissement()->getAdresse(); ?></h2>
	
	<ul id="onglets_majeurs" class="clearfix onglets_stock">
		<?php foreach ($appellations as $app_key => $app):  ?>
		
		<?php $selected = ($app_key==preg_replace('/-[A-Za-z0-9]*$/', '', $appellation_lieu)); ?>
		
		<li class="<?php echo $selected? 'ui-tabs-selected' : '' ; ?>">
			<?php $app_libelle = $app->libelle; 
                              $appellation_lieu_link = ($ds->getAppellationLieuKey($app_key))? $ds->getAppellationLieuKey($app_key) : $app_key ; ?>
			<a href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $appellation_lieu_link)); ?>">
				<span><?php echo (preg_match('/^AOC/', $app_libelle))? 'AOC ' : ''; ?></span> 
				<br><?php echo (preg_match('/^AOC/', $app_libelle))? substr($app_libelle, 4) : $app_libelle; ?>
			</a>
			
			<?php $appellation_k = preg_replace('/-[A-Za-z0-9]*$/', '', $appellation_lieu);?>
			
			<?php if($selected && $ds->hasManyLieux($appellation_k)): ?>
			<?php $has_lieux = true; ?>
				<ul class="sous_onglets">
				  <?php foreach ($ds->getLieuxFromAppellation($appellation_k) as $lieu_key => $lieu) :  
					  $lieu_k = preg_replace('/^lieu/','', $lieu_key);
					  if(preg_replace('/^[A-Z]*-/', '', $appellation_lieu) == $lieu_k) $current_lieu = $lieu;
				  ?>
				  <li class="<?php echo (preg_replace('/^[A-Z]*-/', '', $appellation_lieu) == $lieu_k)? 'ui-tabs-selected' : ''; ?>">
					  <a href="<?php echo url_for('ds_edition_operateur', array('id' => $ds->_id,'appellation_lieu' => $appellation_k.'-'.$lieu_k)); ?>"><?php echo $lieu->getLieuLibelle(); ?></a></li>
				  <?php endforeach; ?>
					<li class="ajouter ajouter_lieu"><a href="#">Ajouter un lieu dit</a></li>
				</ul>
			<?php endif; ?>
		</li>
		<?php endforeach; ?>
		<li>
			<a href="<?php echo url_for("ds_recapitulatif_lieu_stockage", array('id' => $ds->_id)); ?>" style="height: 30px;">
			<br>Récapitulatif</a>
		</li>
	</ul>
		
	<!-- #application_ds -->
	<div id="application_ds" class="clearfix">
		<div id="aucun_produit">
			<p>Il n'y a pas de produit défini pour cette appellation</p>		
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
                                <input type="image" src="/images/boutons/btn_valider.png" alt="Valider" />
			</div>

		</div>			
	</div>
	<!-- fin #application_ds -->

</form>