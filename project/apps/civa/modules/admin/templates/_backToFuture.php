<h3 class="titre_section">Retour dans le passé</h3>
<div class="contenu_section">
    <?php if (acCouchdbManager::getClient("Current")->hasCurrentFromTheFuture()): ?>
        <p>Hey ! Marty ! <br />
           Nous sommes en <?php echo acCouchdbManager::getClient("Current")->getCurrentFromTheFuture()?> !</p>
        <a style="margin-top: 10px;" class="btn_majeur btn_petit btn_jaune" href="<?php echo url_for("admin_back_to_now") ?>">Revenir dans le présent</a>
    <?php else: ?>
        <form action="<?php echo url_for('@admin_back_to_the_future') ?>" method="post">
            <div class="ligne_form ligne_form_label">
                <?php echo $form['campagne']->renderError() ?>
                <?php echo $form['campagne']->renderLabel() ?>
                <?php echo $form['campagne']->render() ?>
            </div>
            <div class="ligne_form ligne_btn">
                <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
            </div>
        </form>
    <?php endif; ?>


</div>
