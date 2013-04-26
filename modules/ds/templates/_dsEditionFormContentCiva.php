<?php
echo $form->renderHiddenFields();
echo $form->renderGlobalErrors();
?>    
    <ul id="liste_cepages">
        <?php foreach ($produits as $hash => $produit) : ?>
    	<li>
    		<small style="font-size:10px"><?php echo $produit->appellation ?></small> <?php echo $produit->libelle; ?>
    	</li>
        <?php endforeach; ?>
    </ul>
    <div id="donnees_stock_cepage">
            <div id="col_hors_vt_sgn" class="colonne">
                            <h2>hors VT et SGN (hl)</h2>

                            <div class="col_cont">
                                <ul>
                                    <?php foreach ($produits as $key => $value) : ?>
                                    <li>
                                        <?php echo $form['volumeStock_'.$key]->render(array('class' => 'num')); ?>
                                        <?php echo $form['volumeStock_'.$key]->renderError(); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
            </div>
            <div id="col_vt" class="colonne">
                            <h2>VT (hl)</h2>

                            <div class="col_cont">
                                <ul>
                                    <?php foreach ($produits as $key => $value) : ?>
                                    <li>
                                        <?php echo $form['vt_'.$key]->render(array('class' => 'num')); ?>
                                        <?php echo $form['vt_'.$key]->renderError(); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
            </div>

            <div id="col_sgn" class="colonne">
                <h2>SGN (hl)</h2>

                <div class="col_cont">
                    <ul>
                        <?php foreach ($produits as $key => $value) : ?>
                        <li>
                            <?php echo $form['sgn_'.$key]->render(array('class' => 'num')); ?>
                            <?php echo $form['sgn_'.$key]->renderError(); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
    </div>

