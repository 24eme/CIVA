<div>
    <ol class="breadcrumb">
        <li><a href="#">Import de contrats</a></li>
        <li><a href="#"><?php echo $compte->nom_a_afficher ?></a></li>
    </ol>

    <?php if ($csvVrac->statut !== CSVVRACClient::LEVEL_IMPORTE): ?>
    <nav class="navbar navbar-default nav-step">
        <ul class="nav navbar-nav">
            <li class="active">
                <a href="#" class=""><span>Import du fichier</span><small class="hidden">Etape 1</small></a>
            </li>
            <li class="active">
                <a href="#" class=""><span>Contenu du fichier</span><small class="hidden">Etape 2</small></a>
            </li>
            <li class="active">
                <a href="#" class=""><span>Validation</span><small class="hidden">Etape 3</small></a>
            </li>
        </ul>
    </nav>

    <h3>Validation avant l'import</h3>

    <div class="alert alert-info">
        <p><strong>Vous êtes sur le point d'importer <?php echo count($vracimport->getContratsImportables()) ?> contrats.</strong></p>
    </div>

    <?php else: ?>
        <h3>Visualisation de l'import</h3>

        <div class="alert alert-info">
            <p><strong>Ces contrats ont déjà été importés (<?php echo count($vracimport->getContratsImportables()) ?> contrats) :</strong></p>
            <ul>
                <?php foreach ($csvVrac->documents as $vracid): ?>
                    <li><a href="<?php echo url_for('vrac_fiche', ['numero_contrat' => str_replace('VRAC-', '', $vracid)]); ?>"><?php echo $vracid ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif ?>

    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#liste" aria-controls="liste" role="tab" data-toggle="tab">Liste des contrats</a></li>
            <li role="presentation"><a href="#fichier" aria-controls="fichier" role="tab" data-toggle="tab">Contenu du fichier</a> </li>
            <li role="presentation" class="pull-right"><small>(<a href="<?php echo url_for('vrac_csv_download', ['csvvrac' => $csvVrac->_id]) ?>">télécharger le fichier</a>)</small></li>
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
                                <p>
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
        <?php foreach ($csvVrac->getAnnexes() as $annexe): ?>
            <object type="application/pdf" style="height: 30vh; width: 100%" data="<?php echo $annexe ?>#toolbar=0&navpanes=0&scrollbar=0&statusbar=0&messages=0&scrollbar=0"></object>
        <?php endforeach; ?>
    </div>
    <?php endif ?>

    <?php if ($csvVrac->statut !== CSVVRACClient::LEVEL_IMPORTE): ?>
    <div class="clearfix form-control-static">
        <form method="POST" id="formimport" action="<?php echo url_for('vrac_csv_import', ['csvvrac' => $csvVrac->_id]) ?>"></form>
        <a href="<?php echo url_for('vrac_csv_fiche', ['csvvrac' => $csvVrac->_id]) ?>" class="btn btn-default">Revenir aux annexes</a>
        <button type="submit" form="formimport" class="btn btn-primary pull-right">Importer les <?php echo count($vracimport->getContratsImportables()) ?> contrats</button>
    </div>
    <?php else: ?>
        <a href="<?php echo url_for('vrac_csv_liste', ['identifiant' => $csvVrac->identifiant]) ?>" class="btn btn-default">Revenir à la liste des imports</a>
    <?php endif ?>
</div>
