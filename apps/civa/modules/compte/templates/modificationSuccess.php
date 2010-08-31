<!-- #principal -->
<h2 class="titre_principal">Mon compte</h2>
<!-- #application_dr -->
<div id="application_dr" class="clearfix">

    <!-- #exploitation_administratif -->
    <div id="mon_compte">

        <h2 class="titre_section">Identifiants</h2>
        <div class="contenu_section" id="modification_compte">
            <div class="presentation clearfix"<?php if ($form_modif_err) echo ' style="display:none;"'; ?>>
                <p class="intro">Vos identifiants de connexion :</p>
                <p><span>Email :</span> <?php echo $email; ?></p>
                <p><span>Mot de passe :</span> ****** </p>
                <div class="btn">
                    <a href="#" class="modifier"><img src="/images/boutons/btn_modifier_infos.png" alt="Modifier les informations" /></a>
                </div>
            </div>


            <div class="modification clearfix"<?php if (!$form_modif_err) echo ' style="display:none;"'; ?>>
                <p class="intro">Modification de vos identifiants de connexion :</p>
                <form method="POST" action="">

                    <div class="ligne_form ligne_form_label">
                        <?php echo $form->renderHiddenFields(); ?>
                        <?php echo $form->renderGlobalErrors(); ?>

                        <?php echo $form['email']->renderError() ?>
                        <?php echo $form['email']->renderLabel() ?>
                        <?php echo $form['email']->render(array('value'=>$email)) ?>
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

                    <div class="btn">
                        <a href="#" class="annuler"><img src="/images/boutons/btn_annuler.png" alt="Annuler" /></a>
                        <input type="image" src="/images/boutons/btn_valider.png" alt="Valider" />
                    </div>
                </form>
            </div>


        </div>
    </div>

</div>
<!-- fin #exploitation_administratif -->

<!-- fin #application_dr -->

<!-- fin #principal -->
