<h2 class="titre_principal" style="background-color: blue">Accueil</h2>
<!-- #application_dr -->
<div class="clearfix" id="application_dr">

    <!-- #nouvelle_declaration -->
    <div id="nouvelle_declaration" style="width: 504px;">
        <form action="<?php echo url_for('@admin-login') ?>" method="post" id="principal">
            <h3 class="titre_section" style="background-color: blue">Connexion</h3>
            <div class="contenu_section">
                <p class="intro">Pour vous connecter, merci d'indiquer votre login :</p>
                <?php echo $form->renderHiddenFields(); ?>
                <?php echo $form->renderGlobalErrors(); ?>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['login']->renderError() ?>
                    <?php echo $form['login']->renderLabel() ?>
                    <?php echo $form['login']->render() ?>
                </div>
                <div class="ligne_form ligne_btn">
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
            </div>
        </form>
    </div>
</div>

