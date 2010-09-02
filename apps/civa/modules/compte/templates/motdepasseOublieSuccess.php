<!-- #principal -->
<h2 class="titre_principal">Mon compte</h2>

<!-- #application_dr -->
<div class="clearfix" id="application_dr">

    <!-- #nouvelle_declaration -->
    <div id="nouvelle_declaration">
        <h3 class="titre_section">Mot de passe oublié</h3>
        <div class="contenu_section">

            <?php if($sf_user->hasFlash('email_send')) { ?>
                <p class="intro"><?php echo $sf_user->getFlash('email_send'); ?></p>
                <p><a href="@compte">Retour</a></p>
            <?php }else { ?>
            <form action="<?php echo url_for('compte/motdepasseOublie') ?>" method="post" id="principal">
                <p class="intro">Merci d'indiquer votre numéro CVI :</p>

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
            </form>
            <?php } ?>

        </div>
    </div>
    <!-- fin #nouvelle_declaration -->

    <!-- #precedentes_declarations -->

    <!-- fin #precedentes_declarations -->
</div>
<!-- fin #application_dr -->

<!-- fin #principal -->
