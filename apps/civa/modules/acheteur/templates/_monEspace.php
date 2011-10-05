<div id="acheteurs">
    <h3 class="titre_section">Acheteurs</h3>
    <div class="contenu_section">
        <ul>
            <li><a href="<?php echo url_for('@export_dr_acheteur_csv') ?>">Télécharger l'import</a></li>
            <li><a href="<?php echo url_for('@export_dr_acheteur_csv?force=1') ?>">Regénérer et télécharger l'import</a></li>
            <li><a href="<?php echo url_for('@upload_csv') ?>">Tester l'exporter un CSV de vos acheteurs</a></li>
        </ul>
    </div>
</div>
