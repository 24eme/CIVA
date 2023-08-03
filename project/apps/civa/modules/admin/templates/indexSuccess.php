<?php include_partial('tiers/ongletsAdmin', array('active' => 'accueil')) ?>
<style>
.ui-autocomplete .ui-menu-item a {
    font-size: 14px;
    padding: 5px 8px;
}

.ui-autocomplete .ui-menu-item:nth-child(even) a {
    background-color: #f5f5f5 !important;
}


</style>
    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration" style="width: 504px;">
            <form id="form_admin_login" action="<?php echo url_for('admin') ?>" method="post" id="principal">
            <h3 class="titre_section">Séléctionner un établissement</h3>
            <div class="contenu_section">
                <p class="intro">Rechercher un établissement (par nom, cvi, civaba, n°accisses, famille, commune) :</p>
                <?php echo $form->renderHiddenFields(); ?>
                <?php echo $form->renderGlobalErrors(); ?>
                <div class="">
                    <?php echo $form['identifiant']->renderError() ?>
		            <?php echo $form['identifiant']->render(array("autofocus" => "autofocus", "class" => "combobox")) ?>
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
            $('#form_admin_login').submit();
        });
    });
    </script>
