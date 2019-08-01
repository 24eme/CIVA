<?php use_helper('Text') ?>
<?php $produits = $form->getProduitsDetails(); ?>
<?php
$isVci = preg_match("/genreVCI$/",$lieu->getKey());
$vciNode = $lieu->getKey();
 ?>
    <ul id="liste_cepages">
        <?php
        foreach ($produits as $key => $detail) :
          if($isVci){
            if(!preg_match("/$vciNode/",$detail->getHash())){ continue; }
          }
            ?>
    	<li>
    		<?php echo $detail->getLibelle() ?>&nbsp;<small style="font-size:10px"><?php echo truncate_text($detail->lieu, 21, "...", false); ?></small>
    	</li>
        <?php endforeach; ?>
	<?php if((count($produits) < count($lieu->getConfig()->getProduits())) || ($lieu->getRawValue() instanceof DSLieu && $lieu->getConfig()->hasLieuEditable())): ?>
		<li class="ajout">
			<a class="ajax" href="<?php echo url_for('ds_ajout_produit', $lieu) ?>">
				<img src="/images/boutons/btn_ajouter_produit.png" alt="Ajouter un produit" />
			</a>
		</li>
        <?php endif; ?>
    </ul>
    <div id="donnees_stock_cepage">
            <div id="col_hors_vt_sgn" class="colonne">
				<h2>
                    <?php if($form->hasVTSGN()): ?>
                    hors VT et SGN (hl)
                    <?php else: ?>
                    Volume (hl)
                    <?php endif; ?>
                </h2>

				<div class="col_cont">
					<ul>
                        <?php $tabindex = 1; ?>
						<?php foreach ($produits as $key => $detail) :
							$key = $detail->getHashForKey();
              if($isVci){
                  if(!preg_match("/$vciNode/",$detail->getHash())){ continue; }

                }
							?>
						<li>
                            <?php if(($produit_key && $key == $produit_key) || (!$produit_key && $tabindex == 1)): ?>
							<?php echo $form[DSCivaClient::VOLUME_NORMAL.$key]->render(array('class' => 'num stock', 'tabindex' => $tabindex, 'autofocus' => 'autofocus')); ?>
                            <?php else: ?>
                                <?php echo $form[DSCivaClient::VOLUME_NORMAL.$key]->render(array('class' => 'num stock', 'tabindex' => $tabindex)); ?>
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
              if($isVci){
                if(!preg_match("/$vciNode/",$detail->getHash())){ continue; }

              }
              ?>
						<li>
							<?php
							if(!$detail->getCepage()->no_vtsgn){
								echo $form[DSCivaClient::VOLUME_VT.$key]->render(array('class' => 'num stock', 'tabindex' => $tabindex));
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
                             if($isVci){
                               if(!preg_match("/$vciNode/",$detail->getHash())){ continue; }

                             }
                            ?>
                        <li>
                            <?php
                            if(!$detail->getCepage()->no_vtsgn){
                                echo $form[DSCivaClient::VOLUME_SGN.$key]->render(array('class' => 'num stock', 'tabindex' => $tabindex));
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
