<div class="alert alert-success" style="margin-top: 20px;">
    Votre fichier est conforme
</div>

<h3>Souhaitez vous ajouter une annexe aux contrats à importer ?</h3>

<p>Vous pouvez ajouter une ou plusieurs annexes <strong>au format PDF</strong> à <strong>tous les contrats</strong> qui seront importés (optionnel)</p>

<form method="POST" enctype='multipart/form-data' action="<?php echo url_for('vrac_csv_fiche', ['csvvrac' => $csvVrac->_id]) ?>">
    <div class="row" style="padding: 10px 0">
        <div class="form-group col-xs-8">
            <label for="annexeInputFile">Ajouter des fichiers d'annexes</label>
            <?php echo $formAnnexe['annexeInputFile']->render(['id' => 'annexeInputFileAdd', 'class' => 'form-control']); ?>
        </div>
    </div>

    <template id="annexeInputFileList">
      <div>
        <h5>Vous allez associer aux contrats les annexes suivantes :</h5>
        <table class="table table-condensed">
            <thead><tr><th>Nom du fichier</th></tr></thead>
            <tbody></tbody>
        </table>
      </div>
    </template>

    <div class="row hidden" style="padding: 10px 0">
        <div class="form-group col-xs-8">
            <?php echo $formAnnexe['annexeInputFile']->render(['name' => 'annexeInputFile[]', 'class' => 'form-control']); ?>
        </div>
    </div>

    <?php if (count($csvVrac->_attachments) > 1): ?>
        <h5>Annexes déjà téléversées :</h5>
        <table class="table table-condensed">
            <thead><tr><th>Nom du fichier</th></tr></thead>
            <tbody>
                <?php foreach ($csvVrac->getAnnexes() as $filename => $file): ?>
                    <tr><td><?php echo $filename ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
      </div>
    <?php endif ?>

    <div class="text-right" style="margin-top: 30px;">
        <a href="<?php echo url_for('vrac_csv_fiche', ['csvvrac' => $csvVrac->_id]) ?>" class="btn pull-left btn-default disabled"><span class="glyphicon glyphicon-chevron-left"></span> Précédent</a>
        <button class="btn btn-success">Continuer <span class="glyphicon glyphicon-chevron-right"></span</button>
    </div>
</form>
