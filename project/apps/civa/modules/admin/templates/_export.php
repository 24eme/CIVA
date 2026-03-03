<h3 class="titre_section">Taches et exports</h3>
<div class="contenu_section">
    <ul>
        <li><a href="<?php echo url_for('@task_list') ?>">Tâches récurrentes</a></li>
        <li><a href="<?php echo url_for('@csv_comptes') ?>">Comptes</a></li>
        <li><a href="<?php echo sfConfig::get('app_export_url') ?>">Exports des données brutes</a></li>
    </ul>
</div>
