<!-- #principal -->
<form action="<?php echo url_for('@tiers') ?>" method="post" id="principal" name ="firstConnection">

    <h2 class="titre_principal">Vos profils</h2>

    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
            <h3 class="titre_section">Choisissez un profil</h3>
            <div class="contenu_section">
                <p class="intro"><?php include_partial('global/message', array('id' => 'msg_tiers_index_intro')); ?></p>
                <?php echo $form->renderHiddenFields(); ?>
                <?php echo $form->renderGlobalErrors(); ?>
                <?php echo $form['tiers']->renderError() ?>
                <?php foreach($form->getChoiceTiers() as $key => $value): ?>
                    <div class="ligne_form">
                        <input type="radio" id="tiers_tiers_<?php echo $key ?>" name="tiers[tiers]" value="<?php echo $key ?>" />
                            <label for="tiers_tiers_<?php echo $key ?>"><?php echo $value ?></label>
                    </div>
                <?php endforeach; ?>
                <div class="ligne_form ligne_btn">
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
            </div>
        </div>
        <!-- fin #nouvelle_declaration -->

        <!-- #precedentes_declarations -->

        <!-- fin #precedentes_declarations -->
    </div>
    <!-- fin #application_dr -->

</form>
<!-- fin #principal -->

