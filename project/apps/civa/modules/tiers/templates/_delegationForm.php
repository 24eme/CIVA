
    <div id="nouvelle_declaration" style="width: 504px;">

    <form name="delegation" id="form_delegation" action="<?php echo url_for('@delegation') ?>" method="POST">
        <h3 class="titre_section">Connexion à un compte</h3>
        <div class="contenu_section">
            <p class="intro">Pour vous connecter, merci d'indiquer le login :</p>
            <?php echo $form->renderHiddenFields(); ?>
            <?php echo $form->renderGlobalErrors(); ?>
            <div class="ligne_form ligne_form_label">
                <?php echo $form['compte']->renderError() ?>
                <?php echo $form['compte']->renderLabel() ?>
                <?php echo $form['compte']->render() ?>
            </div>

            <div class="ligne_form ligne_btn">
                <input type="submit" id="mon_espace_civa_valider" name="bouton" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
            </div>
        </div>
    </form>
</div>
