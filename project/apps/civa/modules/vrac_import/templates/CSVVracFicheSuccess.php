<div>
    <?php include_partial('vrac_import/breadcrumb', ['compte' => $compte]); ?>
    <?php include_partial('vrac_import/step', ['step' => (($csvVrac->statut === CSVVRACClient::LEVEL_ERROR) ? 'import' : 'annexes'), 'csvVrac' => $csvVrac]); ?>

    <?php if ($csvVrac->statut === CSVVRACClient::LEVEL_ERROR): ?>
        <?php include_partial('vrac_import/fiche_erreur', compact('csvVrac', 'vracimport', 'compte')) ?>
    <?php else: ?>
        <?php include_partial('vrac_import/fiche_conforme', compact('csvVrac', 'vracimport', 'compte', 'formAnnexe')) ?>
    <?php endif; ?>
</div>
