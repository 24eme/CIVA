<!-- #principal -->
<form action="<?php echo url_for('@login_admin') ?>" method="post" id="principal">

    <h2 class="titre_principal">Login - Administration</h2>

    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
            <h3 class="titre_section">Connexion</h3>
            <div class="contenu_section">
                <!--<p class="intro">Pour vous connecter, merci d'indiquer votre numéro CVI :</p>-->
                <?php echo $form->renderHiddenFields(); ?>
                <?php echo $form->renderGlobalErrors(); ?>
                <?php if ($need_login): ?>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['username']->renderError() ?>
                    <?php echo $form['username']->renderLabel() ?>
                    <?php echo $form['username']->render() ?>
                </div>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['password']->renderError() ?>
                    <?php echo $form['password']->renderLabel() ?>
                    <?php echo $form['password']->render() ?>
                </div>
                <?php endif; ?>
                <div class="ligne_form ligne_form_label">
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
    </div>
    <!-- fin #application_dr -->

</form>
<!-- fin #principal -->
