<div>
    <h1 class="titre_principal">Téléversement d'un fichier</h1>
    <div style="padding: 10px 0">
        <form method="POST" enctype='multipart/form-data' class="form-inline" action="<?php echo url_for('vrac_csv_import', ['identifiant' => $compte->identifiant]) ?>">
            <div class="form-group">
                <label for="csvVracInputFile">Fichier csv</label>
                <input type="file" id="csvVracInputFile" name="csvVracInputFile" class="form-control">
            </div>
            <button type="submit" class="btn btn-default">Valider</button>
        </form>
    </div>

    <h1 class="titre_principal">Liste des fichiers vrac importés par <?php echo $compte->nom_a_afficher ?></h1>

    <table class="table">
        <thead>
            <tr>
                <td>Date</td>
                <td>Erreur(s)</td>
                <td>Documents créés</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($csvs as $csv): ?>
            <tr<?php if (count($csv->erreurs)): echo " class='danger'"; endif ?>>
                <td><?php echo DateTime::createFromFormat("Ymd", substr($csv->_id, -11, 8))->format('d/m/Y') ?></td>
                <td><?php echo count($csv->erreurs) ?></td>
                <td><?php echo count($csv->documents) ?></td>
                <td>
                    <a href="<?php echo url_for('vrac_csv_fiche', ['csvvrac' => $csv->_id]) ?>"><i class="glyphicon glyphicon-eye-open"></i> Visualiser</a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <a href="<?php echo url_for('mon_espace_civa_vrac', ['identifiant' => $compte->identifiant]) ?>" class="btn btn-default">Retour à mon espace</a>
</div>
