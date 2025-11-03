<div>
    <?php include_partial('vrac_import/breadcrumb', ['compte' => $compte]); ?>
    <?php include_partial('vrac_import/step', ['step' => 'import']); ?>

    <h3>Téléversement du fichier</h3>
    <div>
        <p>Téléverser le fichier csv contenant les différents contrats à importer.</p>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
        <form method="POST" enctype='multipart/form-data' class="form" action="<?php echo url_for('vrac_csv_create', ['identifiant' => $compte->identifiant]) ?>">
            <div class="form-group">
                <label for="csvVracInputFile">Fichier csv</label>
                <input type="file" id="csvVracInputFile" name="csvVracInputFile" class="form-control" required>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="type_vrac" id="type_vrac" value="vrac_application" required>
                Contrats d'applications
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="type_vrac" id="type_vrac" value="vrac_cadre" required>
                Contrats cadres
              </label>
            </div>
            <button type="submit" class="btn btn-default">Valider</button>
        </form>
        </div>
    </div>
    <hr/>
    <h3>Historique</h3>
    <a href="<?php echo url_for('vrac_csv_liste', ['identifiant' => $compte->identifiant]) ?>">Voir les précédents fichiers téléversés</a>
</div>
<div style="padding-top: 1rem">
    <a href="<?php echo url_for('mon_espace_civa_vrac', ['identifiant' => $compte->identifiant]) ?>" class="btn btn-default">Retour à mon espace</a>
</div>
