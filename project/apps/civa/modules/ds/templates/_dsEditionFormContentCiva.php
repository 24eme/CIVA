<?php
$produits = $form->getProduitsDetails();
echo $form->renderHiddenFields();
echo $form->renderGlobalErrors();
?>    
    <ul id="liste_cepages">
        <?php 
        foreach ($produits as $key => $detail) :
            ?>
    	<li>
    		<?php echo $detail->getCepage()->libelle ?>&nbsp;<small style="font-size:10px"><?php echo $detail->lieu; ?></small> 
    	</li>
        <?php endforeach; ?>
		
		<li class="ajout">
			<a href="#">
				<img src="/images/boutons/btn_ajouter_produit.png" alt="Ajouter un produit" />
			</a>
		</li>
    </ul>
    <div id="donnees_stock_cepage">
            <div id="col_hors_vt_sgn" class="colonne">
				<h2>hors VT et SGN (hl)</h2>

				<div class="col_cont">
					<ul>
						<?php foreach ($produits as $key => $detail) : 
							$key = $detail->getHashForKey();
							?>
						<li>
							<?php echo $form[DSCivaClient::VOLUME_NORMAL.$key]->render(array('class' => 'num')); ?>
							<?php echo $form[DSCivaClient::VOLUME_NORMAL.$key]->renderError(); ?>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
            </div>
            <div id="col_vt" class="colonne">
				<h2>VT (hl)</h2>

				<div class="col_cont">
					<ul>
						<?php foreach ($produits as $key => $detail) :
							$key = $detail->getHashForKey();
							?>
						<li>
							<?php
							if(!$detail->getCepage()->no_vtsgn){
								echo $form[DSCivaClient::VOLUME_VT.$key]->render(array('class' => 'num'));
								echo $form[DSCivaClient::VOLUME_VT.$key]->renderError(); 
							}
							?>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
            </div>

            <div id="col_sgn" class="colonne">
                <h2>SGN (hl)</h2>

                <div class="col_cont">
                    <ul>
                        <?php foreach ($produits as $key => $detail) : 
                             $key = $detail->getHashForKey();
                            ?>
                        <li>
                            <?php
                            if(!$detail->getCepage()->no_vtsgn){
                                echo $form[DSCivaClient::VOLUME_SGN.$key]->render(array('class' => 'num'));
                                echo $form[DSCivaClient::VOLUME_SGN.$key]->renderError(); 
                            }
                            ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
    </div>

