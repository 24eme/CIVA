<!-- #principal -->
<form action="<?php echo url_for('@login') ?>" method="post" id="principal">

    <h2 class="titre_principal">Mon espace CIVA</h2>

    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
            <h3 class="titre_section">Connexion</h3>
            <div class="contenu_section">
                <p class="intro">Pour vous connecter, merci d'indiquer votre numéro CVI :</p>
                                    
                    <div class="ligne_form">
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <?php echo $form['cvi']->renderError() ?>
    <?php echo $form['cvi']->renderLabel() ?>
    <?php echo $form['cvi']->render() ?>
                    </div>
<div class="ligne_form ligne_btn">
                        <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                    </div>
                                            </div>
                        </div>
                        <!-- fin #nouvelle_declaration -->

                        <!-- #precedentes_declarations -->
                        
                            <!-- fin #precedentes_declarations -->
                        </div>
                        <!-- fin #application_dr -->

    </form>
<!-- fin #principal -->
