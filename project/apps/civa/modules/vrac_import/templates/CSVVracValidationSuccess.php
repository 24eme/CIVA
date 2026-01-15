<style>
    table#table_contrat tr td {
        border-right-style: dashed;
        border-left-style: dashed;
    }
</style>
<div>
    <?php include_partial('vrac_import/breadcrumb', ['compte' => $compte]); ?>

    <?php if ($csvVrac->statut !== CSVVRACClient::LEVEL_IMPORTE): ?>
        <?php include_partial('vrac_import/step', ['step' => (($csvVrac->statut === CSVVRACClient::LEVEL_ERROR) ? 'import' : 'validation'), 'csvVrac' => $csvVrac]); ?>

        <h3>Validation avant la génération</h3>

    <div class="alert alert-info">
        <p><strong>Vous êtes sur le point de générer <?php echo count($vracimport->getContratsImportables()) ?> contrats.</strong></p>
    </div>

    <?php else: ?>
        <h3>Visualisation de l'import</h3>

        <div class="alert alert-success">
            <p><strong>Ces <?php echo count($vracimport->getContratsImportables()) ?> contrats ont déjà été générés.</strong></p>
        </div>
    <?php endif ?>

    <div>
        <a class="pull-right btn btn-link btn-sm" href="<?php echo url_for('vrac_csv_download', ['csvvrac' => $csvVrac->_id]) ?>">Télécharger le fichier csv importé</a>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#liste" aria-controls="liste" role="tab" data-toggle="tab">Liste des contrats <span class="badge"><?php echo count($vracimport->getContratsImportables()) ?></span></a></li>
            <li role="presentation"><a href="#fichier" aria-controls="fichier" role="tab" data-toggle="tab">Contenu du fichier</a> </li>
            <li role="presentation" class="pull-right"><small></small></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="liste">
                <table id="table_contrat" class="table table-bordered table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th class="col-xs-1">N° Interne</th>
                            <th>Soussigné(s)</th>
                            <th>Produit</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-center">Prix</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php foreach ($vracimport->display() as $numero_contrat => $contrat): ?>
                    <tr>
                        <td><span title="<?php echo ucfirst(strtolower($contrat['type_contrat'])) ?>" class="icon-<?php echo strtolower($contrat['type_contrat']) ?>"></span><?php if($contrat['temporalite_contrat'] == VracClient::TEMPORALITE_PLURIANNUEL_APPLICATION): ?><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-file" viewBox="0 0 16 16" ><path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/></svg><?php else: ?><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-journals" viewBox="0 0 16 16"><path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/><path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0z"/></svg><?php endif; ?></td>
                        <td><?php echo $numero_contrat ?></td>
                        <td>
                            <?php echo $contrat['soussignes']['vendeur']->raison_sociale ?>
                            <?php if ($contrat['soussignes']['courtier']): ?>
                                (via : <?php echo $contrat['soussignes']['courtier']->raison_sociale ?>)
                            <?php endif ?>
                        </td>
                        <td><?php foreach ($contrat['produits'] as $produit_info): ?><?php echo $produit_info['libelle'] ?> <?php echo $produit_info['millesime'] ?><br /><?php endforeach ?></td>
                        <td class="text-right"><?php foreach ($contrat['produits'] as $produit_info): ?><?php echo str_replace(" ", "&nbsp;", $produit_info['volume']) ?><br /><?php endforeach ?></td>
                        <td class="text-right"><?php foreach ($contrat['produits'] as $produit_info): ?><?php echo str_replace([" ","/"], "&nbsp;", $produit_info['prix']) ?><br /><?php endforeach ?></td>
                    </tr>
                <?php endforeach; ?>
                    </tbody>
                </table>
                <?php /*foreach ($vracimport->display() as $numero_contrat => $contrat): ?>
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
                <?php endforeach; */ ?>
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
        <button type="submit" form="formimport" class="btn btn-success pull-right">Générer les <?php echo count($vracimport->getContratsImportables()) ?> contrats <span class="glyphicon glyphicon-ok"></span></button>
    </div>
    <?php else: ?>
        <a href="<?php echo url_for('mon_espace_civa_vrac', ['identifiant' => $compte->identifiant]) ?>" class="btn btn-default">Retour à mon espace</a>
    <?php endif ?>
</div>
