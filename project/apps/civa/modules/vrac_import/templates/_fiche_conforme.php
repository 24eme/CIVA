<?php if($sf_user->hasFlash('fileSuccess')): ?>
<div class="alert alert-success" style="margin-top: 20px;">
    Votre fichier est conforme
</div>
<?php endif; ?>

<form method="POST" enctype='multipart/form-data' action="<?php echo url_for('vrac_csv_fiche', ['csvvrac' => $csvVrac->_id]) ?>" class="form-horizontal">

<?php echo $formAnnexe->renderHiddenFields(); ?>
<?php echo $formAnnexe->renderGlobalErrors(); ?>
<?php echo $formAnnexe['annexeInputFile']->renderError() ?>

<h3>Souhaitez vous ajouter une annexe aux contrats à importer ?</h3>

<p>Vous pouvez ajouter une ou plusieurs annexes <strong>au format PDF</strong> à <strong>tous les contrats</strong> qui seront importés</p>

    <table class="table table-condensed" style="margin-bottom: 10px;">
        <thead><tr><th>Annexes ajoutées</th></tr></thead>
        <tbody>
            <?php foreach ($csvVrac->getAnnexes() as $filename => $file): ?>
                <tr><td><?php echo $filename ?></td></tr>
            <?php endforeach; ?>
        </tbody>
        <tbody style="border-top: none;" id="annexes_container">
        </tbody>
    </table>

    <div class="form-group">
        <label for="annexeInputFile" class="col-sm-3 control-label">Ajouter des fichiers d'annexes</label>
        <div class="col-sm-9">
            <?php echo $formAnnexe['annexeInputFile']->render(['id' => 'annexeInputFileAdd', 'class' => 'form-control', 'name' => null]); ?>
        </div>
    </div>

    <div class="row hidden" style="padding: 10px 0">
        <div class="form-group col-xs-8">
            <?php echo $formAnnexe['annexeInputFile']->render(['name' => 'annexeInputFile[]', 'class' => 'form-control']); ?>
        </div>
    </div>

    <div class="text-right" style="margin-top: 30px;">
        <a href="<?php echo url_for('vrac_csv_fiche', ['csvvrac' => $csvVrac->_id]) ?>" class="btn pull-left btn-default disabled"><span class="glyphicon glyphicon-chevron-left"></span> Précédent</a>
        <button class="btn btn-success">Continuer <span class="glyphicon glyphicon-chevron-right"></span</button>
    </div>
</form>
