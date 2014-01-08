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
                    <?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_VALIDE)): ?>
                    <a href="<?php echo url_for('mon_espace_civa_dr') ?>">Visualiser ma déclaration <?php echo $sf_user->getCampagne() ?></a>
                    <?php elseif(CurrentClient::getCurrent()->isDREditable()): ?>
                    <a href="<?php echo url_for('mon_espace_civa_dr') ?>">A valider avant le 10/12/<?php echo $sf_user->getCampagne() ?></a> <br />
                    <a href="<?php echo url_for('mon_espace_civa_dr') ?>">A valider avant le 10/12/<?php echo $sf_user->getCampagne() ?></a>
                    <?php else: ?>
                    <a href="<?php echo url_for('mon_espace_civa_dr') ?>">Accéder</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DR_APPORTEUR)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> recolte">
                <div class="bloc_acceuil_header">Alsace Récolte</div>
                <div class="bloc_acceuil_content">
                    <?php if($sf_user->getTiers('MetteurEnMarche')->gamma->statut == "INSCRIT"): ?>
                        <a href="<?php echo url_for(sfConfig::get('app_gamma_url_prod')) ?>">Accéder</a>
                    <?php else: ?>
                        <a href="<?php echo url_for('mon_espace_civa_gamma') ?>">Accéder</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::VRAC)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> contrats">
                <div class="bloc_acceuil_header">Alsace Contrats</div>
                <div class="bloc_acceuil_content">
                    <?php if(count($vracs) > 0): ?>
                        <a href="<?php echo url_for('mon_espace_civa_vrac') ?>"><?php echo count($vracs) ?> contrat(s) non signé(s)</a>
                    <?php else: ?>
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
                    <?php if($sf_user->getTiers('MetteurEnMarche')->gamma->statut == "INSCRIT"): ?>
                        <a href="<?php echo url_for(sfConfig::get('app_gamma_url_prod')) ?>">Accéder</a>
                    <?php else: ?>
                        <a href="<?php echo url_for('mon_espace_civa_gamma') ?>">Accéder</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DS)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> bloc_acceuil_last <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> stocks">
                <div class="bloc_acceuil_header">Alsace stocks</div>
                <div class="bloc_acceuil_content">
                    <?php if($sf_user->hasLieuxStockage() && $sf_user->getDs() && $sf_user->getDs()->isValideeTiers()): ?>
                        <a href="<?php echo url_for('mon_espace_civa_ds') ?>">Visualiser ma déclaration <?php echo $sf_user->getCampagne() ?></a>
                    <?php elseif($sf_user->hasLieuxStockage() && CurrentClient::getCurrent()->isDSEditable()): ?>
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