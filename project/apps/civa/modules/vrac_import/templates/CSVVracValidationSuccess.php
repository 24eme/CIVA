<div>
    <?php include_partial('vrac_import/breadcrumb', ['compte' => $compte]); ?>

    <?php if ($csvVrac->statut !== CSVVRACClient::LEVEL_IMPORTE): ?>
    <?php include_partial('vrac_import/step', ['step' => (($csvVrac->statut === CSVVRACClient::LEVEL_ERROR) ? 'import' : 'validation'), 'csvVrac' => $csvVrac]); ?>

    <h3>Validation avant l'import</h3>

    <div class="alert alert-info">
        <p><strong>Vous êtes sur le point d'importer <?php echo count($vracimport->getContratsImportables()) ?> contrats.</strong></p>
    </div>

    <?php else: ?>
        <h3>Visualisation de l'import</h3>

        <div class="alert alert-success">
            <p><strong>Ces <?php echo count($vracimport->getContratsImportables()) ?> contrats ont déjà été importés</strong></p>
        </div>
    <?php endif ?>

    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#liste" aria-controls="liste" role="tab" data-toggle="tab">Liste des contrats</a></li>
            <li role="presentation"><a href="#fichier" aria-controls="fichier" role="tab" data-toggle="tab">Contenu du fichier</a> </li>
            <li role="presentation" class="pull-right"><small>(<a href="<?php echo url_for('vrac_csv_download', ['csvvrac' => $csvVrac->_id]) ?>">télécharger le fichier csv</a>)</small></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="liste">
                <?php foreach ($vracimport->display() as $numero_contrat => $contrat): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title">Contrat n° <?php echo $numero_contrat ?></div>
                        </div>
                        <div class="panel-body">
                            <div class="col-xs-4">
                                Entre <strong>l'acheteur</strong> :<br/>
                                <?php echo $contrat['soussignes']['acheteur']->raison_sociale ?><br/>
                                et <strong>le vendeur</strong> :<br/>
                                <?php echo $contrat['soussignes']['vendeur']->raison_sociale ?><br/>
                                <?php if ($contrat['soussignes']['courtier']): ?>
                                    Via <strong>le courtier</strong> :<br/>
                                    <?php echo $contrat['soussignes']['courtier']->raison_sociale ?> (<abbr title="Courtier">C</abbr>)
                                <?php endif ?>
                            </div>
                            <div class="col-xs-8">
                                <strong>Produits du contrat :</strong>
                                <?php foreach ($contrat['produits'] as $produit_info): ?>
                                <p style="margin-bottom: 0">
                                <?php echo $produit_info['libelle'] ?> <?php echo $produit_info['millesime'] ?> de <?php echo $produit_info['volume'] ?> à <?php echo $produit_info['prix'] ?>
                                </p>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="fichier">
                <?php include_partial('vrac_import/contenu_fichier', compact('vracimport', 'csvVrac')); ?>
            </div>
        </div>
    </div>

    <?php if (count($csvVrac->getAnnexes())): ?>
    <div>
        <?php if ($csvVrac->statut !== CSVVRACClient::LEVEL_IMPORTE): ?>
            <p class="lead">Chaque contrats aura pour annexes le ou les documents suivants :</p>
        <?php else: ?>
            <p class="lead">Les annexes suivantes ont été ajoutées aux contrats créés :</p>
        <?php endif ?>
        <?php foreach ($csvVrac->getAnnexes() as $annexe => $uri): ?>
            <p class="titre_principal">Annexe : <?php echo $annexe ?></p>
            <object type="application/pdf" style="height: 30vh; width: 100%" data="<?php echo url_for('vrac_csv_attachment', ['csvvrac' => $csvVrac->_id, 'attachment' => $annexe]) ?>#toolbar=0&navpanes=0&scrollbar=0&statusbar=0&messages=0&scrollbar=0"></object>
        <?php endforeach; ?>
    </div>
    <?php endif ?>

    <?php if ($csvVrac->statut !== CSVVRACClient::LEVEL_IMPORTE): ?>
    <div class="clearfix form-control-static" style="margin-top: 10px;">
        <form method="POST" id="formimport" action="<?php echo url_for('vrac_csv_import', ['csvvrac' => $csvVrac->_id]) ?>"></form>
        <a href="<?php echo url_for('vrac_csv_fiche', ['csvvrac' => $csvVrac->_id]) ?>" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span> Précédent</a>
        <button type="submit" form="formimport" class="btn btn-success pull-right">Importer les <?php echo count($vracimport->getContratsImportables()) ?> contrats <span class="glyphicon glyphicon-ok"></span></button>
    </div>
    <?php else: ?>
        <a href="<?php echo url_for('mon_espace_civa_vrac', ['identifiant' => $compte->identifiant]) ?>" class="btn btn-default">Retour à mon espace</a>
    <?php endif ?>
</div>
