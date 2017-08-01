<!-- #principal -->
    <!-- #application_ds -->
    <div id="application_ds" class="clearfix">
        <div id="feed_back">
            <h2 class="titre_section">Votre avis</h2>
            <div class="contenu_section">
                <div class="bloc_vert">
                    <p class="important">Votre retour d'expérience nous intéresse</p>
                    <p>N'hésitez pas à indiquer ci-dessous tout commentaire que vous souhaiteriez nous faire parvenir à propos de la saisie de la déclaration de Stocks :</p>
                    <div class="bloc_form">
                        <form action="" method="post">
                            <?php echo $form->renderGlobalErrors(); ?>
                            <?php echo $form['message']->renderError(); ?>
                            <?php echo $form['message']->render(); ?>
                            <?php echo $form->renderHiddenFields(); ?>
                            <div class="ligne_form ligne_btn">
                                <input type="image" name="boutons[valider]" id="mon_espace_civa_valider" class="btn" src="/images/boutons/btn_valider.png" alt="Valider" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- fin #application_ds -->

    <ul id="btn_etape" class="btn_prev_suiv clearfix">
    <li class="prec">
        <a href="<?php echo url_for('mon_espace_civa_ds', array("type" => $type_ds, "identifiant" => $identifiant)); ?>">
            <img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à l'étape précédente" />
        </a>
    </li>
    </ul>
<!-- fin #principal -->
