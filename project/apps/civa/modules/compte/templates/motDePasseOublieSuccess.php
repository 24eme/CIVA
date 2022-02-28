<!-- #principal -->
<h2 class="titre_principal">Mon compte</h2>

<!-- #application_dr -->
<div class="clearfix" id="application_dr">

    <!-- #nouvelle_declaration -->
    <div id="nouvelle_declaration" class="mot_de_passe_oublie">
        <h3 class="titre_section">Mot de passe oublié</h3>
        <div class="contenu_section">
            <form action="<?php echo url_for('@compte_mot_de_passe_oublie') ?><?php if($service): ?>?service=<?php echo $service ?><?php endif; ?>" method="post" id="principal">
                <p class="intro">Merci d'indiquer votre identifiant :</p>

                <div class="ligne_form ligne_form_label">
                    <?php echo $form->renderHiddenFields(); ?>
                    <?php echo $form->renderGlobalErrors(); ?>

                    <?php echo $form['login']->renderError() ?>
                    <?php echo $form['login']->renderLabel() ?>
                    <?php echo $form['login']->render() ?>
                </div>
                <div class="ligne_form ligne_btn">
                    <input type="submit" alt="Valider" value="Valider" class="btn btn_majeur btn_petit btn_vert">
                </div>
            </form>
        </div>
    </div>
    <!-- fin #nouvelle_declaration -->

    <!-- #precedentes_declarations -->

    <!-- fin #precedentes_declarations -->
</div>
<!-- fin #application_dr -->

<!-- fin #principal -->
