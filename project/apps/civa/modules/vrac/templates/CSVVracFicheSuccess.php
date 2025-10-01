<div id="application_dr" class="mon_espace_civa">
    <h1 class="titre_principal">Bilan de l'import des contrats</h1>
    <div class="contenu">

        <h3 class="titre_section">Récapitulatif du fichier</h3>

        <?php if ($csvVrac->hasErreurs()): ?>
            <div class="alert alert-danger">
                Votre fichier comporte des erreurs. Vous ne pouvez pas importer vos contrats sans modification de votre fichier.
            </div>
            <table class="table table-bordered">
            <?php for ($i = 1; $i <= count($vracimport->getCsv()); $i++): ?>
                <?php if ($listeerreurs = $csvVrac->getErreurs($i)->getRawValue()): ?>
                <tr>
                    <td><a href="#line<?php echo $i ?>">#<?php echo $i ?></a></td>
                    <td>
                        <table>
                        <?php foreach ($listeerreurs as $e): ?>
                            <tr><td><?php echo $e->diagnostic; ?></td></tr>
                        <?php endforeach ?>
                        </table>
                    </td>
                <tr>
                <?php endif ?>
            <?php endfor; ?>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                Total de ligne dans le fichier : <strong><?php echo count($vracimport->getCsv()) ?></strong><br>
                Contrats importés : <strong><?php echo count($csvVrac->getDocuments()) ?></strong>
                <ul>
                    <?php foreach ($csvVrac->getDocuments() as $import): ?>
                    <li>
                        <a href="<?php echo url_for('vrac_fiche', ['numero_contrat' => str_replace('VRAC-', '', $import)]) ?>"><?php echo $import ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if ($csvVrac->statut !== CSVVRACClient::LEVEL_IMPORTE): ?>
                <h3 class="titre_section">Annexes</h3>
                <p>Ajouter une annexe à tous les contrats ? (Optionel)</p>

                <form method="POST" enctype='multipart/form-data' action="<?php echo url_for('vrac_csv_import', ['csvvrac' => $csvVrac->_id]) ?>">
                    <div style="padding: 10px 0">
                        <div class="form-group">
                            <label for="annexeInputFile">Fichier csv</label>
                            <?php echo $formAnnexe['annexeInputFile']->render(['class' => 'form-control']); ?>
                        </div>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary">Importer</button>
                    </div>
                </form>
            <?php endif ; ?>
        <?php endif; ?>

        <h3 class="titre_section">Contenu du fichier importé</h3>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th>Ligne</th>
                        <?php foreach ($vracimport->getHeaders() as $header): ?>
                            <th><?php echo $header ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vracimport->getCsv() as $num => $line): ?>
                    <tr id="line<?php echo $num + 1 ?>" class="<?php echo count($csvVrac->getErreurs($num + 1)) ? 'danger' : '' ?>">
                        <td><?php echo $num + 1 ?></td>
                        <?php foreach ($line as $td): ?>
                            <td><?php echo $td ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center">
            <a href="<?php echo url_for('vrac_csv_liste', ['identifiant' => $csvVrac->identifiant]) ?>" class="btn btn-default">Retour à la liste</a>
        </div>

    </div>
</div>
