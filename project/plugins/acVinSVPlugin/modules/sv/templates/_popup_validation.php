<div class="popup_validation_sv" class="popup_ajout popup_confirme" title="Validation de votre SV">
  <form method="POST" action="">
    <p>
        Une fois votre déclaration validée, vous ne pourrez plus la modifier. <br /><br />
        Confirmez vous la validation de votre déclaration de récolte ?<br />
    </p>

    <div id="administration_validation">
        <h2 class="titre_section">Validation</h2>
        <div class="contenu_section">
            <div class="bloc_gris presentation">
              <div class="bloc_form">
                <?php echo $form->renderGlobalErrors(); ?>
                <?php echo $form->renderHiddenFields(); ?>
                <div class="ligne_form">
                    <?php echo $form['date']->renderLabel(null, array('style' => 'display: inline-block;')); ?>
                    <?php echo $form['date']->renderError(); ?>
                    <?php echo $form['date']->render(array('class' => "datepicker")); ?>
                </div>
              </div>
            </div>
        </div>
    </div>
  </form>
</div>

<script>
var initConfirmeValidationSV = function() {
    $('#valideSV').click(function() {
        openPopup($("#popup_confirme_validation"));
        return false;
    });
    $('#valideSV_OK').click(function() {
        $("#popup_confirme_validation").dialog('close');
        $("#principal").submit();
        return false;
    });
}
</script>
