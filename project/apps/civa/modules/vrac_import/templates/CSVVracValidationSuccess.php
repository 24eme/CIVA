<div>
    <ol class="breadcrumb">
        <li><a href="#">Import de contrats</a></li>
        <li><a href="#"><?php echo $compte->nom_a_afficher ?></a></li>
    </ol>

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

    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#liste" aria-controls="liste" role="tab" data-toggle="tab">Liste des contrats à créer</a></li>
            <li role="presentation"><a href="#fichier" aria-controls="fichier" role="tab" data-toggle="tab">Contenu du fichier</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="liste">
                <?php // $vracimport->display(); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="fichier">
                <?php include_partial('vrac_import/contenu_fichier', compact('vracimport', 'csvVrac')); ?>
            </div>
        </div>
    </div>
</div>
