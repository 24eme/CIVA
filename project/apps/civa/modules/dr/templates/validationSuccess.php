<?php include_partial('dr/etapes', array('etape' => 4, 'dr' => $dr)) ?>
<?php include_partial('dr/actions', array('etape' => 4, 'help_popup_action'=> $help_popup_action)) ?>

<!-- #principal -->
<form id="principal" action="" method="post" class="ui-tabs">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#recolte_totale">Récolte totale</a></li>
    </ul>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <div id="validation_dr">
        <p class="intro_declaration"><?php echo acCouchdbManager::getClient('Messages')->getMessage('intro_validation'); ?></p>

        <?php if($error && !empty($validLogErreur)): ?>
            <fieldset class="message message_erreur">
                <legend class="message_title">Points bloquants <a href="" class="msg_aide" rel="help_popup_validation_log_erreur" title="Message aide"></a> </legend>
                <ul class="messages_log">
                <?php foreach($validLogErreur as $key=>$log): ?>
                    <li>
                        <?php echo $log['info'] ?>&nbsp;:&nbsp;<a href="<?php echo url_for('dr_recolte_erreur_log', array('id' => $dr->_id, 'array' => 'log_erreur', 'flash_message' => $key)); ?>"><?php echo $log['log']; ?></a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </fieldset>
        <?php endif; ?>
        <?php if($logVigilance){ ?>
            <fieldset class="message">
                <legend class="message_title">Points de vigilance <a href="" class="msg_aide" rel="help_popup_validation_log_vigilance" title="Message aide"></a></legend>
                <ul class="messages_log">
                    <?php foreach($validLogVigilance as $key=>$log): ?>
                    <li>
                        <?php echo $log['info'] ?>&nbsp;:&nbsp;<a href="<?php echo url_for('dr_recolte_erreur_log', array('id' => $dr->_id, 'array' => 'log_vigilance', 'flash_message'=> $key)); ?>"><?php echo $log['log']; ?></a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($dr->recolte->getTotalVolumeVendus() > 0 && !$dr->recolte->canCalculVolumeRevendiqueSurPlace() && !$dr->recolte->getTotalVci()): ?>
                <strong><?php echo acCouchdbManager::getClient('Messages')->getMessage('err_log_pas_calculer_revendique_sur_place') ?></strong>
                <?php endif; ?>
                <?php if ($dr->recolte->getTotalVolumeVendus() > 0 && !$dr->recolte->canCalculVolumeRevendiqueSurPlace() && $dr->recolte->getTotalVci() > 0): ?>
                <strong>Le volume revendiqué sur place ne peut pas être calculé car tous les volumes en dépassement et/ou tous les volumes de vci de vos acheteurs n'ont pas été saisis dans le récapitulatif des ventes. Si vous n'affectez pas de dépassement et/ou pas de vci à vos acheteurs, tapez 0 dans la zone "dont dépassement" et/ou "dont vci"</strong>
                <?php endif; ?>


            </fieldset>
        <?php } ?>

        <!-- #acheteurs_caves -->
        <?php include_component('dr', 'recapDeclaration', array('dr' => $dr)) ?>
        <!-- fin #acheteurs_caves -->
        </div>
    </div>
    <!-- fin #application_dr -->
	  <?php if ($annee == $sf_user->getCampagne()) : ?>
	    <?php if($error){ ?>
            <?php include_partial('dr/boutons', array('display' => array('precedent','previsualiser'), 'dr' => $dr)) ?>
        <?php }else{?>
            <?php include_partial('dr/boutons', array('display' => array('precedent','previsualiser','valider'), 'dr' => $dr)) ?>
        <?php } ?>
    <?php endif; ?>
</form>
<!-- fin #principal -->

<?php include_partial('dr/generationDuPdf', array('annee' => $annee, 'etablissement' => $dr->getEtablissement())) ?>

<?php include_partial('dr/popupConfirmeValidation', array('dr' => $dr, 'formDatesModification' => isset($formDatesModification) ? $formDatesModification : null, 'validation_compte_id' => $validation_compte_id)) ?>
