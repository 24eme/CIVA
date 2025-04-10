<div>
    <h1 class="titre_principal">Liste des fichiers vrac importés par <?php echo $compte->nom_a_afficher ?></h1>

    <table class="table">
        <thead>
            <tr>
                <td>Date</td>
                <td>Erreur(s)</td>
                <td>Action</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($csvs as $csv): ?>
            <tr>
                <td><?php echo DateTime::createFromFormat("Ymd", substr($csv->_id, -11, 8))->format('d/m/Y') ?></td>
                <td><?php echo count($csv->erreurs) ?></td>
                <td>
                    <a href="<?php echo url_for('vrac_csv_fiche', ['csvvrac' => $csv->_id]) ?>"><i class="glyphicon glyphicon-eye-open"></i> Visualiser</a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <a href="<?php echo url_for('mon_espace_civa_vrac', ['identifiant' => $compte->identifiant]) ?>" class="btn btn-default">Retour à mon espace</a>
</div>
