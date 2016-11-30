<!-- #principal -->
<h2 class="titre_principal">Mon compte</h2>
<!-- #application_dr -->
<div id="application_dr" class="clearfix">

    <!-- #exploitation_administratif -->
    <div id="mon_compte">

        <h2 class="titre_section">Identifiants</h2>
        <div class="contenu_section" id="modification_compte">
            <div class="presentation clearfix"<?php if ($form->hasErrors()) echo ' style="display:none;"'; ?>>
                <p class="intro">Informations correspondant à votre compte :</p>
                <?php if($sf_user->hasFlash('maj')) : ?>
                    <p class="flash_message"><?php echo $sf_user->getFlash('maj'); ?></p>
                <?php endif; ?>
                <p><span>Identifiant :</span> <?php echo $compte->identifiant; ?></p>
                <p><span>Email :</span> <?php echo $compte->email; ?></p>
                <p><span>Mot de passe :</span> ****** </p>
                <div class="btn">
                    <a href="#" class="modifier"><img src="/images/boutons/btn_modifier_infos.png" alt="Modifier les informations" /></a>
                </div>
            </div>


            <div class="modification clearfix"<?php if (!$form->hasErrors()) echo ' style="display:none;"'; ?>>
                <p class="intro">Modification des informations de votre compte :</p>

                <form method="post" action="<?php echo url_for("@compte_modification") ?><?php if($service): ?>?service=<?php echo $service ?><?php endif; ?>">
                    <div class="ligne_form ligne_form_label">
                        <label for="compte_email">Identifiant : </label>
                        <p><?php echo $compte->login; ?></p>
                    </div>
                    <div class="ligne_form ligne_form_label">
                        <?php echo $form->renderHiddenFields(); ?>
                        <?php echo $form->renderGlobalErrors(); ?>

                        <?php echo $form['email']->renderError() ?>
                        <?php echo $form['email']->renderLabel() ?>
                        <?php echo $form['email']->render() ?>
                    </div>
                    <div class="ligne_form ligne_form_label">
                        <?php echo $form['mdp1']->renderError() ?>
                        <?php echo $form['mdp1']->renderLabel() ?>
                        <?php echo $form['mdp1']->render() ?>
                    </div>
                    <div class="ligne_form">
                        <?php echo $form['mdp2']->renderError() ?>
                        <?php echo $form['mdp2']->renderLabel() ?>
                        <?php echo $form['mdp2']->render() ?>
                    </div>

		    <input type="hidden" value="<?php echo $redirect;?>" name="redirect"/>

                    <div class="btn">
                        <a href="#" class="annuler"><img src="/images/boutons/btn_annuler.png" alt="Annuler" /></a>
                        <input type="image" src="/images/boutons/btn_valider.png" alt="Valider" />
                    </div>
                </form>
            </div>


        </div>
        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
            <p><br /><a class="admin" href="<?php echo url_for("compte_droits") ?>">Gestion des droits/comptes</a></p>
        <?php endif; ?>
    </div>

</div>
<!-- fin #exploitation_administratif -->

<!-- fin #application_dr -->

<!-- fin #principal -->
