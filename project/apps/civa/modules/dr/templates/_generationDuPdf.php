<script>
    <?php if (isset($from_csv)): ?>
    ajax_url_to_print = "<?php echo url_for("dr_pdf", array('identifiant' => $etablissement->identifiant, 'annee' => $annee, 'ajax' => 1, 'from_csv' => 1)); ?>";
    <?php else: ?>
    ajax_url_to_print = "<?php echo url_for("dr_pdf", array('identifiant' => $etablissement->identifiant, 'annee' => $annee, 'ajax' => 1)); ?>";
    <?php endif; ?>
</script>
<div style="display: none" id="popup_loader" title="Génération du PDF">
    <div class="popup-loading">
    <p>La génération de votre PDF est en cours.<br />Merci de patienter.<br /><small>La procédure peut prendre 30 secondes</small></p>
    </div>
</div>
