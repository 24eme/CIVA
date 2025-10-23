<div>
    <ol class="breadcrumb">
        <li><a href="#">Import de contrats</a></li>
        <li><a href="#"><?php echo $compte->nom_a_afficher ?></a></li>
    </ol>

    <nav class="navbar navbar-default nav-step">
        <ul class="nav navbar-nav">
            <li class="active">
                <a href="#" class=""><span>Import du fichier</span><small class="hidden">Etape 1</small></a>
            </li>
            <li class="disabled">
                <a href="#" class=""><span>Contenu du fichier</span><small class="hidden">Etape 2</small></a>
            </li>
            <li class="disabled">
                <a href="#" class=""><span>Validation</span><small class="hidden">Etape 3</small></a>
            </li>
        </ul>
    </nav>

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
