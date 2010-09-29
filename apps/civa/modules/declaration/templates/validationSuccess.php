<?php include_partial('global/etapes', array('etape' => 3)) ?>
<?php include_partial('global/actions', array('etape' => 3, 'help_popup_action'=>$help_popup_action)) ?>

<!-- #principal -->
<form id="principal" action="" method="post" class="ui-tabs">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#recolte_totale">Récolte totale</a></li>
    </ul>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <div id="validation_dr">
        <p class="intro_declaration"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_validation'); ?></p>

        <?php if($error && !empty($validLogErreur)){ ?>
            <fieldset class="message message_erreur">
                <legend class="message_title">Points bloquants <a href="" class="msg_aide" rel="help_popup_validation_log_erreur" title="Message aide">Teste message d'aide</a> </legend>
                <ul class="messages_log">
                <?php foreach($validLogErreur as $key=>$log) { ?>
                
                    <li><a href="<?php echo url_for('recolte_erreur_log', array('array'=>'log_erreur', 'flash_message'=>$key)); ?>"><?php echo $log['log']; ?></a></li>
                   
                <?php } ?>
                </ul>
            </fieldset>
        <?php } ?>
        <?php if($logVigilance){ ?>
            <fieldset class="message">
                <legend class="message_title">Points de vigilance <a href="" class="msg_aide" rel="help_popup_validation_log_vigilance" title="Message aide">Teste message d'aide</a></legend>
                <ul class="messages_log">
                    <?php foreach($validLogVigilance as $key=>$log) { ?>
                    <li><a href="<?php echo url_for('recolte_erreur_log', array('array'=>'log_vigilance', 'flash_message'=>$key)); ?>"><?php echo $log['log']; ?></a></li>
                    <?php } ?>
                </ul>
            </fieldset>
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

<?php include_partial('generationDuPdf', array('annee' => $annee)) ?>

<?php include_partial('popupConfirmeValidation') ?>
