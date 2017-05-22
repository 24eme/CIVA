<form method="post" action="">
 	<h2 class="titre_principal">Gestion des droits</h2>
    <div class="clearfix" id="application_dr">
    	 <div id="nouvelle_declaration">
            <h3 class="titre_section">
            <?php if($form->getObject()->isNew()): ?>
                Ajouter un compte
            <?php else: ?>
                Modifier le compte
            <?php endif; ?>
            </h3>
            <div class="contenu_section">
   				<p class="intro">
                    <?php if($form->getObject()->isNew()): ?>
                        Entrez les informations pour ajouter un compte
                    <?php else: ?>
                        Entrez les informations pour modifier le compte
                    <?php endif; ?>
                </p>

                <div class="ligne_form ligne_form_label">
                    <?php echo $form->renderHiddenFields(); ?>
                    <?php echo $form->renderGlobalErrors(); ?>

                    <?php echo $form['nom']->renderError() ?>
                    <?php echo $form['nom']->renderLabel() ?>
                    <?php echo $form['nom']->render() ?>
                </div>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['email']->renderError() ?>
                    <?php echo $form['email']->renderLabel() ?>
                    <?php echo $form['email']->render() ?>
                </div>

                <div class="ligne_form ligne_btn">
                    <a class="annuler" href="<?php echo url_for("compte_droits") ?>"><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
            </div>
        </div>
    </div>
</form>
