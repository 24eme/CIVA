<div id="application_dr" class="mon_espace_civa">
    <h1 class="titre_principal">Bilan de l'import des contrats</h1>
    <div class="contenu">

        <h3 class="titre_section">Erreurs dans le fichier</h3>

        <?php if ($csvVrac->hasErreurs()): ?>
            <div class="row bg-secondary">
                <?php for ($i = 1; $i <= count($vracimport->getCsv()); $i++): ?>
                    <?php if ($listeerreurs = $csvVrac->getErreurs($i)): ?>
                        <div class="col-xs-1">
                            <a href="#line<?php echo $i ?>">#<?php echo $i ?></a>
                        </div>
                        <div class="col-xs-11">
                            <?php foreach ($listeerreurs as $e): ?>
                                <p><?php echo $e->diagnostic; ?></p>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-success">
                Pas d'erreur dans le fichier
            </div>
        <?php endif ?>

        <h3 class="titre_section">Fichier importé</h3>

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

        <?php if ($csvVrac->statut === CSVVRACClient::LEVEL_IMPORTE): ?>
            <div class="alert alert-info">
                Ces contrats ont déjà été importés
            </div>

            <div class="text-center">
                <a href="<?php echo url_for('vrac_csv_liste', ['identifiant' => $csvVrac->identifiant]) ?>" class="btn btn-default">Retour à la liste</a>
            </div>
        <?php elseif ($csvVrac->hasErreurs()): ?>
            <div class="alert alert-danger">
                Votre fichier comporte des erreurs. Vous ne pouvez pas importer vos contrats.
            </div>

            <div class="text-center">
                <a href="<?php echo url_for('vrac_csv_liste', ['identifiant' => $csvVrac->identifiant]) ?>" class="btn btn-default">Retour à la liste</a>
            </div>
        <?php else: ?>
            <h3 class="titre_section">Annexes</h3>
            <p>Ajouter une annexe à tous les contrats ? (Optionel)</p>

            <form method="POST" enctype='multipart/form-data' action="<?php echo url_for('vrac_csv_import', ['csvvrac' => $csvVrac->_id]) ?>">
                <div style="padding: 10px 0">
                    <div class="form-group">
                        <label for="annexeInputFile">Fichier csv</label>
                        <input type="file" id="annexeInputFile" name="annexeInputFile" class="form-control">
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-primary">Importer</button>
                    <a href="<?php echo url_for('vrac_csv_liste', ['identifiant' => $csvVrac->identifiant]) ?>" class="btn btn-default">Retour à la liste</a>
                </div>
            </form>
        <?php endif ?>
    </div>
</div>
