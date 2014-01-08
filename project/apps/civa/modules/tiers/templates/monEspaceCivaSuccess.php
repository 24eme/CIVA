<?php include_partial('global/actions', array('etape' => 0, 'help_popup_action' => $help_popup_action)) ?>

<?php include_partial('tiers/title') ?>

<div id="application_dr" class="mon_espace_civa clearfix">

    <?php include_partial('tiers/onglets', array('active' => 'accueil')) ?>

    <div class="contenu">
        <?php if($sf_user->hasFlash('confirmation')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
        <?php endif; ?>
        <h3 class="noir">Vos échéances</h3>
        <div class="blocs_accueil_container_<?php echo $nb_blocs ?>">
            <?php $i = $nb_blocs ?>
            <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DR)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> recolte">
                <div class="bloc_acceuil_header">Alsace Récolte</div>
                <div class="bloc_acceuil_content">
                    <?php if(CurrentClient::getCurrent()->isDREditable() && !$sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_VALIDE)): ?>
                    <a href="<?php echo url_for('mon_espace_civa_dr') ?>">A valider avant le 10/12/<?php echo $sf_user->getCampagne() ?></a> <br />
                    <?php else: ?>
                    <a href="<?php echo url_for('mon_espace_civa_dr') ?>">Accéder</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DR_ACHETEUR)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> recolte">
                <div class="bloc_acceuil_header">Alsace Récolte</div>
                <div class="bloc_acceuil_content">
                     <a href="<?php echo url_for('mon_espace_civa_dr_acheteur') ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::VRAC)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> contrats">
                <div class="bloc_acceuil_header">Alsace Contrats</div>
                <div class="bloc_acceuil_content">
                    <?php $acceder = true ?>
                    <?php if($vracs['CONTRAT_A_TERMINER']): ?>
                        <a href="<?php echo url_for('mon_espace_civa_vrac') ?>"><?php echo $vracs['CONTRAT_A_TERMINER'] ?> contrat(s) à finir de créer</a><br />
                    <?php $acceder = false ?>
                    <?php endif; ?>
                    <?php if($vracs['CONTRAT_A_SIGNER']): ?>
                        <a href="<?php echo url_for('mon_espace_civa_vrac') ?>"><?php echo $vracs['CONTRAT_A_SIGNER'] ?> contrat(s) à signer</a><br />
                    <?php $acceder = false ?>
                    <?php endif; ?>
                    <?php if($vracs['CONTRAT_EN_ATTENTE_SIGNATURE']): ?>
                        <a href="<?php echo url_for('mon_espace_civa_vrac') ?>"><?php echo $vracs['CONTRAT_EN_ATTENTE_SIGNATURE'] ?> contrat(s) en attente de signature</a><br />
                    <?php $acceder = false ?>
                    <?php endif; ?>
                    <?php if($vracs['CONTRAT_A_ENLEVER']): ?>
                        <a href="<?php echo url_for('mon_espace_civa_vrac') ?>"><?php echo $vracs['CONTRAT_A_ENLEVER'] ?> contrat(s) à enlever</a><br />
                    <?php $acceder = false ?>
                    <?php endif; ?>
                    <?php if($acceder): ?>
                    <a href="<?php echo url_for('mon_espace_civa_vrac') ?>">Accéder</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::GAMMA)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> gamma">
                <div class="bloc_acceuil_header">Alsace Gamm@</div>
                <div class="bloc_acceuil_content">
                    <a href="<?php echo url_for('mon_espace_civa_gamma') ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DS)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> bloc_acceuil_last <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> stocks">
                <div class="bloc_acceuil_header">Alsace stocks</div>
                <div class="bloc_acceuil_content">
                    <?php if($sf_user->hasLieuxStockage() && CurrentClient::getCurrent()->isDSEditable() && $sf_user->getDs() && $sf_user->getDs()->isValideeTiers()): ?>
                        <a href="<?php echo url_for('mon_espace_civa_ds') ?>">A valider avant le 31/08/<?php echo $sf_user->getCampagne() ?></a>
                    <?php else: ?>
                        <a href="<?php echo url_for('mon_espace_civa_ds') ?>">Accéder</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
    </div>
</div>