<form action="<?php echo url_for('@login_admin') ?>" method="post" id="principal">

    <h2 class="titre_principal">Administration</h2>

    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
            <h3 class="titre_section">Connexion</h3>
            <div class="contenu_section">
                <p class="intro">Pour vous connecter, merci d'indiquer un numéro de CVI :</p>
                <?php echo $form->renderHiddenFields(); ?>
                <?php echo $form->renderGlobalErrors(); ?>
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
        <div id="precedentes_declarations">
            <h3 class="titre_section">Actions</h3>
            <div class="contenu_section">
                <ul>
                    <li><a href="<?php echo url_for('@csv_tiers') ?>">Export CSV des tiers</a></li>
                </ul>
            </div>
        </div>
    </div>
</form>
