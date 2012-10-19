<div id ="application_dr">
<div id="nouveau_cvi" style="width: 504px;">

    <form name="new_cvi" id="form_new_cvi" action="<?php echo url_for('@migration_compte') ?>" method="POST">
        <h3 class="titre_section">Création d' un nouveau compte</h3>
        <div class="contenu_section" >
            <p class="intro">Pour vous connecter, merci d'indiquer le login :</p>
            <?php echo $form->renderHiddenFields(); ?>
            <?php echo $form->renderGlobalErrors(); ?>
            <div class="ligne_form ligne_form_label">
                <?php echo $form['ancien_cvi']->renderError() ?>
                <?php echo $form['ancien_cvi']->renderLabel() ?>
                <?php echo $form['ancien_cvi']->render() ?>
            </div>
            <div class="ligne_form ligne_form_label">
                <?php echo $form['nouveau_cvi']->renderError() ?>
                <?php echo $form['nouveau_cvi']->renderLabel() ?>
                <?php echo $form['nouveau_cvi']->render() ?>
            </div>
            <div class="ligne_form ligne_btn" style="display: inline-block">
                <input type="submit" id="mon_espace_civa_valider" name="bouton" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
            </div>
            <?php if(isset($success) && $success) : ?>
            <div id='infos_traitement' style="display: inline-block">
                <div id="etape_1">Création du compte                     OK</div>
                <div id="etape_2">Création des tiers associés            OK   </div>
                <div id="etape_3">Récupération de l' historique          OK</div>
                <div id="etape_4">La création du compte est terminée     OK</div>
            </div>
            <?php endif; ?>
        </div>

      </form>

    </div>
</div>