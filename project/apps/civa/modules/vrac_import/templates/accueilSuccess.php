<div>
    <?php include_partial('vrac_import/breadcrumb', ['compte' => $compte]); ?>
    <?php include_partial('vrac_import/step', ['step' => 'import']); ?>

    <h3>Téléversement du fichier </h3>
    <div>
        <p>Téléverser le fichier csv contenant les différents contrats à importer.</p>
    </div>
        <form id="form_csv" method="POST" enctype='multipart/form-data' class="form" action="<?php echo url_for('vrac_csv_create', ['identifiant' => $compte->identifiant]) ?>">
            <div class="row">
            <div class="form-group col-xs-8">
                <label for="csvVracInputFile">Fichier csv</label><a target="_blank" class="btn btn-link btn-sm" href="https://github.com/24eme/CIVA/blob/master/docs/logiciels_tiers/Contrats.md"><span class="glyphicon glyphicon-info-sign"></span> Voir la documentation du format attendu</a>
                <input type="file" id="csvVracInputFile" name="csvVracInputFile" class="form-control" required>
            </div>
            </div>
            <label style="margin-top: 10px;">Type de contrat</label>
            <div style="margin-top: 5px;" class="radio">
              <label>
                <input type="radio" name="type_vrac" id="type_vrac" value="<?php echo CSVVRACClient::TYPE_CONTRAT_PLURIANNUEL_CADRE ?>" required>
                Contrats cadres
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" name="type_vrac" id="type_vrac" value="<?php echo CSVVRACClient::TYPE_CONTRAT_PLURIANNUEL_APPLICATION ?>" required>
                Contrats d'applications
              </label>
               <!-- <a class="btn btn-link btn-sm" href="">(Télécharger le csv préprempli)</a> -->
            </div>

        </form>
</div>
<div style="padding-top: 2rem;" class="row">
    <div class="col-xs-4"><a href="<?php echo url_for('mon_espace_civa_vrac', ['identifiant' => $compte->identifiant]) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Retour à mon espace</a></div>
    <div class="col-xs-4 text-center"><a class="btn btn-default" href="<?php echo url_for('vrac_csv_liste', ['identifiant' => $compte->identifiant]) ?>">Voir l'historique des fichiers téléversés</a></div>
    <div class="col-xs-4 text-right"><button type="submit" form="form_csv" class="btn btn-success">Continuer <span class="glyphicon glyphicon-chevron-right"></span></button></div>


</div>
