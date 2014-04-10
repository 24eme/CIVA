<?php use_helper('ds'); ?>
<form action="" method="post">
    <?php 
        echo $form->renderHiddenFields();
        echo $form->renderGlobalErrors();
        include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds, 'etape' => 3));
    ?>

    <h2 class="titre_page">Ajout d'un lieu de stockage</h2>
    

    <!-- #application_ds -->
    <div id="application_ds" class="clearfix">
        <div id="aucun_produit">      
            <div class="form_ligne">
                <?php echo $form['nom']->renderLabel(); ?>
                <?php echo $form['nom']->render(array('autofocus' => "autofocus")); ?>
                <?php echo $form['nom']->renderError(); ?>
            </div>
            <div class="form_ligne">
                <?php echo $form['adresse']->renderLabel(); ?>
                <?php echo $form['adresse']->render(array('autofocus' => "autofocus")); ?>
                <?php echo $form['adresse']->renderError(); ?>
            </div>
            <div class="form_ligne">
                <?php echo $form['code_postal']->renderLabel(); ?>
                <?php echo $form['code_postal']->render(array('autofocus' => "autofocus")); ?>
                <?php echo $form['code_postal']->renderError(); ?>
            </div>
            <div class="form_ligne">
                <?php echo $form['commune']->renderLabel(); ?>
                <?php echo $form['commune']->render(array('autofocus' => "autofocus")); ?>
                <?php echo $form['commune']->renderError(); ?>
            </div>
            <div class="form_btn">
                <a href="<?php echo url_for('ds_lieux_stockage',$ds) ?>"><img src="/images/boutons/btn_annuler.png" alt="Annuler" /></a>
                    
                <input type="image" src="/images/boutons/btn_valider.png" alt="Valider" />
            </div>

        </div>          
    </div>
    <!-- fin #application_ds -->
</form>