<?php include_partial('global/actions', array('etape' => 0, 'help_popup_action' => $help_popup_action)) ?>

<?php include_partial('tiers/title') ?>

<div id="application_dr" class="mon_espace_civa clearfix">

    <?php include_partial('tiers/onglets', array('active' => 'accueil')) ?>

    <div class="contenu">
        <?php if($sf_user->hasFlash('confirmation')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
        <?php endif; ?>

        <h3 class="noir">Vos échéances</h3>

        <table class="tableau_echeances">
            <thead>
                <tr>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DR)): ?>
                    <th class="recolte">Alsace Récolte</th>
                    <?php endif; ?>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DR_APPORTEUR)): ?>
                    <th class="recolte">Alsace Récolte</th>
                    <?php endif; ?>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::VRAC)): ?>
                    <th class="contrats">Alsace Contrats</th>
                    <?php endif; ?>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::GAMMA)): ?>
                    <th class="gamma">Alsace Gamm@</th>
                    <?php endif; ?>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DS)): ?>
                    <th class="stocks">Alsace stocks</th>
                    <?php endif; ?>
                    <!--<th class="drm">Alsace DRM</th>-->
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DR)): ?>
                    <td>
                        <?php if($sf_user->hasCredential(myUser::CREDENTIAL_DECLARATION_VALIDE)): ?>
                        <a href="<?php echo url_for('mon_espace_civa_dr') ?>">Visualiser ma déclaration <?php echo $sf_user->getCampagne() ?></a>
                        <?php elseif(CurrentClient::getCurrent()->isDREditable()): ?>
                        <a href="<?php echo url_for('mon_espace_civa_dr') ?>">A valider avant le 10/12/<?php echo $sf_user->getCampagne() ?></a>
                        <?php else: ?>
                        <a href="<?php echo url_for('mon_espace_civa_dr') ?>">Accéder</a>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DR_APPORTEUR)): ?>
                    <td><a href="<?php echo url_for('mon_espace_civa_dr_apporteur') ?>">Accéder</a></td>
                    <?php endif; ?>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::VRAC)): ?>
                        <td class="alt">
                            <?php if(count($vracs) > 0): ?>
                                <a href="<?php echo url_for('mon_espace_civa_vrac') ?>"><?php echo count($vracs) ?> contrat(s) non signé(s)</a>
                            <?php else: ?>
                                <a href="<?php echo url_for('mon_espace_civa_vrac') ?>">Accéder</a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::GAMMA)): ?>
                    <td>
                        <?php if($sf_user->getTiers('MetteurEnMarche')->gamma->statut == "INSCRIT"): ?>
                            <a href="<?php echo url_for(sfConfig::get('app_gamma_url_prod')) ?>">Accéder</a>
                        <?php else: ?>
                            <a href="<?php echo url_for('mon_espace_civa_gamma') ?>">Accéder</a>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DS)): ?>
                    <td class="alt">
                        <?php if($sf_user->hasLieuxStockage() && $sf_user->getDs() && $sf_user->getDs()->isValideeTiers()): ?>
                            <a href="<?php echo url_for('mon_espace_civa_ds') ?>">Visualiser ma déclaration <?php echo $sf_user->getCampagne() ?></a>
                        <?php elseif($sf_user->hasLieuxStockage() && CurrentClient::getCurrent()->isDSEditable()): ?>
                            <a href="<?php echo url_for('mon_espace_civa_ds') ?>">A valider avant le 31/08/<?php echo $sf_user->getCampagne() ?></a>
                        <?php else: ?>
                            <a href="<?php echo url_for('mon_espace_civa_ds') ?>">Accéder</a>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <!--<td><a href="#">A valider avant le 31/08/13</a></td>-->
                </tr>
            </tbody>
        </table>
        
    </div>
</div>
