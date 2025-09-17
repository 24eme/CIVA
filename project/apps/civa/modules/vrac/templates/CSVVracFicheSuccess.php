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
            <table class="table table-bordered">
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
                    <tr id="line<?php echo $num + 1 ?>" class="<?php echo count($csvVrac->getErreurs($num + 1)) ? 'bg-danger' : '' ?>">
                        <td><?php echo $num + 1 ?></td>
                        <?php foreach ($line as $td): ?>
                            <td><?php echo $td ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <a href="<?php echo url_for('vrac_csv_liste', ['identifiant' => $csvVrac->identifiant]) ?>" class="btn btn-default">Retour à la liste</a>
</div>
