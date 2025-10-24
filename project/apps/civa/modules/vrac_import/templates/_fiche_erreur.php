<style>
    tr:target {
        border: 2px dotted red;
    }
</style>

<h3>Récapitulatif du fichier</h3>
<div class="alert alert-danger">
    Votre fichier comporte des erreurs. Vous ne pouvez pas importer vos contrats sans modification de votre fichier.
</div>

<table class="table table-bordered">
<thead><tr><th>Ligne</th><th>Description de l'erreur</th><th>Voir</th></tr></thead>
<tbody>
<?php for ($i = 1; $i <= count($vracimport->getCsv()); $i++): ?>
    <?php if ($listeerreurs = $csvVrac->getErreurs($i)->getRawValue()): ?>
    <tr>
        <td><a href="#line<?php echo $i ?>">Ligne #<?php echo $i ?></a></td>
        <td>
            <table>
            <?php foreach ($listeerreurs as $e): ?>
                <tr><td><?php echo $e->diagnostic; ?></td></tr>
            <?php endforeach ?>
            </table>
        </td>
        <td><a href="#line<?php echo $i ?>"><i class="glyphicon glyphicon-eye-open"></i> Voir la ligne en erreur</a></td>
    <tr>
    <?php endif ?>
<?php endfor; ?>
</tbody>
</table>

<hr/>

<div class="clearfix">
    <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#modal-reupload">
        Reverser un fichier corrigé
    </button>

    <div class="modal fade" id="modal-reupload" tabindex="-1" role="dialog" aria-labelledby="modal-reupload-label">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="modal-reupload-label">Reverser un fichier</h4>
          </div>
          <div class="modal-body">
            <form method="POST" enctype='multipart/form-data' id="form-reupload" class="form" action="<?php echo url_for('vrac_csv_upload', ['csvvrac' => $csvVrac->_id]) ?>">
                <div class="form-group">
                    <label for="csvVracInputFile">Fichier csv</label>
                    <input type="file" id="csvVracInputFile" name="csvVracInputFile" class="form-control" required="required">
                </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" form="form-reupload" class="btn btn-default">Save changes</button>
          </div>
        </div>
      </div>
    </div>
</div>


<h3>Contenu du fichier importé <small>(<a href="<?php echo url_for('vrac_csv_download', ['csvvrac' => $csvVrac->_id]) ?>">télécharger le fichier</a>)</small></h3>

<?php include_partial('vrac_import/contenu_fichier', compact('vracimport', 'csvVrac')); ?>

<div class="clearfix mt-1">
    <a href="<?php echo url_for('vrac_csv_liste', ['identifiant' => $csvVrac->identifiant]) ?>" class="btn btn-default">Retour à la liste</a>
    <button type="button" class="btn btn-default pull-right" data-toggle="modal" data-target="#modal-reupload">
        Reverser un fichier corrigé
    </button>
</div>
