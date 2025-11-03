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
                <a href="#" class=""><span>Annexes</span><small class="hidden">Etape 2</small></a>
            </li>
            <li class="disabled">
                <a href="#" class=""><span>Validation</span><small class="hidden">Etape 3</small></a>
            </li>
        </ul>
    </nav>

    <?php if ($csvVrac->statut === CSVVRACClient::LEVEL_ERROR): ?>
        <?php include_partial('vrac_import/fiche_erreur', compact('csvVrac', 'vracimport', 'compte')) ?>
    <?php else: ?>
        <?php include_partial('vrac_import/fiche_conforme', compact('csvVrac', 'vracimport', 'compte', 'formAnnexe')) ?>
    <?php endif; ?>
</div>
