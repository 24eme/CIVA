<script>
    ajax_url_to_print = "<?php echo url_for('@sendEmail?annee='.$annee); ?>?ajax=1";
</script>
<div style="display: none" id="popup_loader" title="Génération du PDF">
    <div class="popup-loading">
    <p>La génération de votre PDF est en cours.<br/>Merci de patienter.<br/><small>La procédure peut prendre 30 secondes</small></p>
    </div>
</div>
