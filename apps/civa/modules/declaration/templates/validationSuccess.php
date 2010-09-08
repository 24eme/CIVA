<?php include_partial('global/etapes', array('etape' => 3)) ?>
<?php include_partial('global/actions') ?>

<!-- #principal -->
<form id="principal" action="" method="post" class="ui-tabs">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#recolte_totale">Récolte totale</a></li>
    </ul>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <div id="validation_dr">
        <p class="intro_declaration">Veuillez vérifier les informations saisies avant de valider votre déclaration.</p>

        <?php if($error){ ?>
            <div class="message">
                <?php foreach($validLog as $logs) { ?>
                <ul class="messages_log">
                    <?php foreach($logs as $log) { ?>
                        <li><a href="<?php echo $log['url']; ?>"><?php echo $log['log']; ?></a></li>
                   <?php } ?>
                </ul>
                <br />
                <?php } ?>
            </div>
        <?php } ?>

        <!-- #acheteurs_caves -->
        <?php include_component('declaration', 'recapDeclaration') ?>
        <!-- fin #acheteurs_caves -->
        </div>
    </div>
    <!-- fin #application_dr -->
    <?php if ($annee == '2010') : ?>
        <?php if($error){ ?>
            <?php include_partial('global/boutons', array('display' => array('precedent','previsualiser'))) ?>
        <?php }else{?>
            <?php include_partial('global/boutons', array('display' => array('precedent','previsualiser','valider'))) ?>
        <?php } ?>
    <?php endif; ?>
</form>
<!-- fin #principal -->
<script>
    ajax_url_to_print = "<?php echo url_for('@print?annee='.$annee); ?>?ajax=1";
</script>
<div style="display: none" id="popup_loader" title="Génération du PDF">
    <div class="popup-loading">
    <p>La génération de votre PDF est en cours.<br/>Merci de patienter.<br/><small>La procédure peut prendre 30 secondes</small></p>
    </div>
</div>