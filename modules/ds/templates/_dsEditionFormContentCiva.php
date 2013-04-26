
<?php
echo $form->renderHiddenFields();
echo $form->renderGlobalErrors();
?>    
    <ul id="liste_sepages">
    <?php foreach ($produits as $hash => $produit) : ?>
	<li>
		<a href="#"><small style="font-size:10px"><?php echo $produit->appellation ?></small> <?php echo $produit->libelle; ?></a>
	</li>
    <?php    endforeach; ?>
    </ul>
        <div id="donnees_stock_sepage" class="clearfix">
                <div id="col_hors_vt_sgn" class="colonne">
                                <h2>hors VT et SGN (hl)</h2>

                                <div class="col_cont">
                                        <?php foreach ($produits as $key => $embedForm) : ?>
                                                <?php echo $form['volumeStock_'.$key]->render(); ?>
                                                <?php echo $form['volumeStock_'.$key]->renderError(); ?>
                                        <?php    endforeach; ?>
                                </div>
                </div>
                <div id="col_vt" class="colonne">
                                <h2>VT (hl)</h2>

                                <div class="col_cont">
                                            <?php foreach ($produits as $key => $value) : ?>
                                            <?php echo $form['vt_'.$key]->render(); ?>
                                            <?php echo $form['vt_'.$key]->renderError(); ?>
                                        <?php    endforeach; ?>
                                </div>
                </div>

                <div id="col_sgn" class="colonne">
                                <h2>SGN (hl)</h2>

                                <div class="col_cont">
                                            <?php foreach ($produits as $key => $value) : ?>
                                            <?php echo $form['sgn_'.$key]->render(); ?>
                                            <?php echo $form['sgn_'.$key]->renderError(); ?>
                                        <?php    endforeach; ?>
                                </div>
                </div>
        </div>

