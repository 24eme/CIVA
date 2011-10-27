<div id="import">
    <h3 class="titre_section">Import</h3>
    <div class="contenu_section">
        <ul>
            <li><a href="<?php echo url_for('@export_dr_acheteur_csv') ?>">Télécharger l'import</a></li>
            <li><a href="<?php echo url_for('@export_dr_acheteur_csv?force=1') ?>">Regénérer et télécharger l'import</a></li>  
        </ul>
    </div>
</div>


<div id="export">
    <h3 class="titre_section">Export</h3>
    <div class="contenu_section">
        <form class="bloc_vert" action="<?php echo url_for('@mon_espace_civa') ?>" method="POST" enctype="multipart/form-data">
            <div class="form_ligne">
		        <?php echo $formUploadCsv->renderHiddenFields() ?>
		        <?php echo $formUploadCsv->renderGlobalErrors() ?>
		        <?php echo $formUploadCsv['file']->renderError() ?>
		        <?php echo $formUploadCsv['file']->renderLabel() ?>
		        <?php echo $formUploadCsv['file']->render() ?>
            </div>
            <input type="image" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
        </form>
    </div>
</div>