<?php if (isset($emailSent)): ?>
  <div class="alert alert-<?php echo ($emailSent) ? 'success' : 'warning' ?>" role="alert">
    <?php if ($emailSent): ?>
      Votre retour à bien été pris en compte
    <?php else: ?>
      <strong>Attention</strong> Un problème est survenu durant l'envoi de l'email
    <?php endif ?>
  </div>
<?php endif ?>

<!-- #principal -->
    <!-- #application_sv -->
    <div id="application_sv" class="clearfix">
        <div id="feed_back">
            <h2 class="titre_section">Votre avis</h2>
            <div class="contenu_section row">
                <div class="bloc_vert col-xs-8">
                    <p class="important">Votre retour d'expérience nous intéresse</p>
                    <p>N'hésitez pas à indiquer ci-dessous tout commentaire que vous souhaiteriez nous faire parvenir à propos de la saisie de la déclaration de Production :</p>
                    <form action="" method="post">
                        <?php echo $form->renderGlobalErrors(); ?>
                        <?php echo $form['message']->renderError(); ?>
                        <?php echo $form['message']->render(array('class' => 'w-100 form-control', 'required' => true)); ?>
                        <?php echo $form->renderHiddenFields(); ?>
                        <div class="text-right" style="margin-top: 20px;">
                            <button type="submit" name="boutons[valider]" id="mon_espace_civa_valider" class="btn btn-success">Valider</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="text-left">
      <a href="<?php echo url_for('mon_espace_civa_production', $sv->etablissement) ?>"><img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à mon espace CIVA" name="boutons[previous]" /></a>
    </div>
