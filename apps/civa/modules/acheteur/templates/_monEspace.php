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
        <form class="bloc_vert" action="/upload/csv" method="POST" enctype="multipart/form-data">
            <div class="form_ligne">
                <label for="csv_file">Fichier</label>
                <input type="file" name="csv[file]" id="csv_file" />
                <input type="hidden" name="csv[_csrf_token]" value="82c4ba89b3ec0abe1562b8261e265a98" id="csv__csrf_token" />
            </div>
            <input type="image" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
        </form>
    </div>
</div>