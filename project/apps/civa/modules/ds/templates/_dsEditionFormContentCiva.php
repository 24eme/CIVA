<?php
$produits = $form->getProduitsDetails();
?>    
    <ul id="liste_cepages">
        <?php 
        foreach ($produits as $key => $detail) :
            ?>
    	<li>
    		<?php echo $detail->getCepage()->libelle ?>&nbsp;<small style="font-size:10px"><?php echo $detail->lieu; ?></small> 
    	</li>
        <?php endforeach; ?>
	<?php if((count($produits) < count($lieu->getConfig()->getProduitsFilter(ConfigurationAbstract::TYPE_DECLARATION_DS))) || $lieu->getConfig()->hasLieuEditable()): ?>	
		<li class="ajout">
			<a href="<?php echo url_for('ds_ajout_produit', $lieu) ?>">
				<img src="/images/boutons/btn_ajouter_produit.png" alt="Ajouter un produit" />
			</a>
		</li>
        <?php endif; ?>	
    </ul>
    <div id="donnees_stock_cepage">
            <div id="col_hors_vt_sgn" class="colonne">
				<h2>hors VT et SGN (hl)</h2>

				<div class="col_cont">
					<ul>
                        <?php $tabindex = 1; ?>
						<?php foreach ($produits as $key => $detail) : 
							$key = $detail->getHashForKey();
							?>
						<li>
                            <?php if($tabindex == 1): ?>
							<?php echo $form[DSCivaClient::VOLUME_NORMAL.$key]->render(array('class' => 'num', 'tabindex' => $tabindex, 'autofocus' => 'autofocus')); ?>
                            <?php else: ?>
                                <?php echo $form[DSCivaClient::VOLUME_NORMAL.$key]->render(array('class' => 'num', 'tabindex' => $tabindex)); ?>
                            <?php endif; ?>
							<?php echo $form[DSCivaClient::VOLUME_NORMAL.$key]->renderError(); ?>
						</li>
                        <?php $tabindex = $tabindex+3; ?>
						<?php endforeach; ?>
					</ul>
				</div>
            </div>
<?php        if($form->hasVTSGN()): ?>
            <div id="col_vt" class="colonne">
				<h2>VT (hl)</h2>

				<div class="col_cont">
					<ul>
                        <?php $tabindex = 2; ?>
						<?php foreach ($produits as $key => $detail) :
							$key = $detail->getHashForKey();
							?>
						<li>
							<?php
							if(!$detail->getCepage()->no_vtsgn){
								echo $form[DSCivaClient::VOLUME_VT.$key]->render(array('class' => 'num', 'tabindex' => $tabindex));
								echo $form[DSCivaClient::VOLUME_VT.$key]->renderError(); 
							}
							?>
						</li>
                        <?php $tabindex = $tabindex+3; ?>
						<?php endforeach; ?>
					</ul>
				</div>
            </div>

            <div id="col_sgn" class="colonne">
                <h2>SGN (hl)</h2>

                <div class="col_cont">
                    <ul>
                        <?php $tabindex = 3; ?>
                        <?php foreach ($produits as $key => $detail) : 
                             $key = $detail->getHashForKey();
                            ?>
                        <li>
                            <?php
                            if(!$detail->getCepage()->no_vtsgn){
                                echo $form[DSCivaClient::VOLUME_SGN.$key]->render(array('class' => 'num', 'tabindex' => $tabindex));
                                echo $form[DSCivaClient::VOLUME_SGN.$key]->renderError(); 
                            }
                            ?>
                        </li>
                        <?php $tabindex = $tabindex+3; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
<?php endif; ?>
    </div>

