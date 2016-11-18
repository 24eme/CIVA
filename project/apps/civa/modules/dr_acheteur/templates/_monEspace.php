<div id="export">
    <h3 class="titre_section">Export</h3>
    <div class="contenu_section">
        <?php if ($csv): ?>
            <ul>
                <li><a href="<?php echo url_for('csv_download', $etablissement) ?>">Télécharger l'export</a></li>
            </ul>
            <br />
        <?php endif; ?>
        <form class="bloc_vert" action="<?php echo url_for('upload_csv', $etablissement) ?>" method="POST" enctype="multipart/form-data">
            <div class="form_ligne">
                <?php echo $formUploadCsv->renderHiddenFields() ?>
                <?php echo $formUploadCsv->renderGlobalErrors() ?>
                <?php echo $formUploadCsv['file']->renderError() ?>
                <?php echo $formUploadCsv['file']->renderLabel() ?>
                <?php echo $formUploadCsv['file']->render() ?>
            </div>
            <input type="image" class="btn" src="/images/boutons/btn_valider.png" alt="Valider" />
        </form>

    </div>
</div>
