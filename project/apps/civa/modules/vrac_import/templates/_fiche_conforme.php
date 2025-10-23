<h3>Récapitulatif du fichier</h3>

<div>
    <p><strong>Votre fichier est conforme.</strong></p>
    <p>
        Total de ligne dans le fichier : <strong><?php echo count($vracimport->getCsv()) ?></strong><br>
        Total de contrats dans le fichier : <strong><?php echo count($vracimport->getContratsImportables()) ?></strong>
    </p>
    <p>Le détail des contrats avec leurs produits sera visible dans l'étape suivante.</p>
</div>

<h3>Souhaitez vous ajouter une annexe aux contrats à importer ?</h3>

<p>Vous pouvez ajouter une ou plusieurs annexes aux contrats qui seront importés (optionnel)</p>

<form method="POST" enctype='multipart/form-data' action="<?php echo url_for('vrac_csv_import', ['csvvrac' => $csvVrac->_id]) ?>">
    <div style="padding: 10px 0">
        <div class="form-group">
            <label for="annexeInputFile">Fichiers d'annexes</label>
            <?php echo $formAnnexe['annexeInputFile']->render(['name' => 'annexeInputFile[]', 'class' => 'form-control']); ?>
        </div>
        <template id="annexeInputFileList">
          <div>
            <h5>Vous allez associer aux contrats les annexes suivantes :</h5>
            <table class="table table-condensed">
                <thead><th>Nom du fichier</th></head>
                <tbody></tbody>
            </table>
          </div>
        </template>
    </div>
    <div class="text-right">
        <button class="btn btn-primary">Importer</button>
    </div>
</form>
