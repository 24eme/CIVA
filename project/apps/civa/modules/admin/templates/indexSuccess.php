
    <h2 class="titre_principal">Administration</h2>
    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration" style="width: 504px;">
            <form id="form_admin_login" action="<?php echo url_for('admin') ?>" method="post" id="principal">
            <h3 class="titre_section">Connexion à un compte</h3>
            <div class="contenu_section">
                <p class="intro">Pour vous connecter, merci d'indiquer le login :</p>
                <?php echo $form->renderHiddenFields(); ?>
                <?php echo $form->renderGlobalErrors(); ?>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['login']->renderError() ?>
                    <?php echo $form['login']->renderLabel() ?>
                    <?php echo $form['login']->render(array("autofocus" => "autofocus", "class" => "combobox")) ?>
                </div>
                <div class="ligne_form ligne_btn">
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
            </div>
            </form>
            <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
            <br />
            <?php include_partial('admin/gamma') ?>
            <?php endif;?>
        </div>

        <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ADMIN)): ?>
        <div id="precedentes_declarations">
            <?php include_partial('admin/export') ?>
            <br />
            <?php include_partial('admin/statistiques') ?>
            <br />
            <?php include_partial('admin/backToFuture', array('form' => $form_back_future)) ?>
        </div>
        <?php endif; ?>
    </div>
    <script type="text/javascript">
    $(document).ready(function () {
        $("select.combobox").combobox();
        $("select.combobox").live('change', function() {
            $(this).parents('form').submit();
        });
    });
    </script>
