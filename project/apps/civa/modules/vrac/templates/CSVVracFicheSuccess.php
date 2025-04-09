<div id="application_dr" class="mon_espace_civa">
    <h1 class="titre_principal">Bilan de l'import des contrats <?php echo $csvVrac->identifiant ?></h1>
    <div class="contenu">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <?php foreach ($vracimport->getHeaders() as $header): ?>
                            <th><?php echo $header ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vracimport->getCsv() as $line): ?>
                    <tr>
                        <?php foreach ($line as $td): ?>
                            <td><?php echo $td ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
