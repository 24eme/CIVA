<form class="bloc_vert" action="<?php echo url_for('@upload_csv') ?>" method="POST" enctype="multipart/form-data">
    <div class="form_ligne">
        <?php echo $csvform->renderHiddenFields() ?>
        <?php echo $csvform->renderGlobalErrors() ?>
        <?php echo $csvform['file']->renderError() ?>
        <?php echo $csvform['file']->renderLabel() ?>
        <?php echo $csvform['file']->render() ?>
    </div>
    <input type="image" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
</form>