<?php include_partial('tiers/title') ?>

<div id="application_dr" class="mon_espace_civa clearfix">

    <?php include_partial('tiers/onglets', array('active' => 'accueil', 'compte' => $compte)) ?>

    <div class="contenu">
        <?php if($sf_user->hasFlash('confirmation')) : ?>
            <p class="flash_message"><?php echo $compte->getFlash('confirmation'); ?></p>
        <?php endif; ?>
        <h3 class="noir">Vos téléservices</h3>
        <div class="blocs_accueil_container_<?php echo $nb_blocs ?>">
            <?php $i = $nb_blocs ?>
            <?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::DR)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> recolte">
                <div class="bloc_acceuil_header">Alsace Récolte</div>
                <div class="bloc_acceuil_content">
                    <?php if(CurrentClient::getCurrent()->isDREditable()): ?>
                    <p><strong>A valider</strong> avant le 10/12/<?php echo $compte->getCampagne() ?></p>
                    <?php else: ?>
                    <p class="mineure">Aucune information à signaler</p>
                    <?php endif; ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_dr', array('identifiant' => $compte->getIdentifiant())) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::DR_ACHETEUR)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> recolte">
                <div class="bloc_acceuil_header">Alsace Récolte</div>
                <div class="bloc_acceuil_content">
                    <?php if(CurrentClient::getCurrent()->isDREditable()): ?>
                    <p>Le portail est <strong>ouvert</strong></p>
                    <?php else: ?>
                    <p class="mineure">Aucune information à signaler</p>
                    <?php endif; ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_dr_acheteur') ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <!--<div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?>">
                <div class="bloc_acceuil_header">DRM</div>
                <div class="bloc_acceuil_content">
                    <p class="mineure">Aucune information à signaler</p>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('drm_etablissement', array("identifiant" => $compte->getIdentifiant())) ?>">Accéder</a>
                </div>
            </div>
            <?php //$i = $i -1 ?>-->
            <?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::VRAC)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> contrats">
                <div class="bloc_acceuil_header">Alsace Contrats</div>
                <div class="bloc_acceuil_content">
                    <?php $infos = true ?>
                    <?php if($vracs['CONTRAT_A_SIGNER']): ?>
                        <p><?php echo $vracs['CONTRAT_A_SIGNER'] ?> contrat<?php echo ($vracs['CONTRAT_A_SIGNER'] > 1) ? "s" : "" ?> reçu<?php echo ($vracs['CONTRAT_A_SIGNER'] > 1) ? "s" : "" ?> <strong>à signer</strong></p>
                    <?php $infos = false ?>
                    <?php endif; ?>
                    <?php if($vracs['CONTRAT_A_TERMINER']): ?>
                        <p><?php echo $vracs['CONTRAT_A_TERMINER'] ?> contrat<?php echo ($vracs['CONTRAT_A_TERMINER'] > 1) ? "s" : "" ?> <strong>à finaliser</strong></p>
                        <?php $infos = false ?>
                    <?php endif; ?>
                    <?php if($vracs['CONTRAT_EN_ATTENTE_SIGNATURE']): ?>
                        <p><?php echo $vracs['CONTRAT_EN_ATTENTE_SIGNATURE'] ?>&nbsp;contrat<?php echo ($vracs['CONTRAT_EN_ATTENTE_SIGNATURE'] > 1) ? "s" : "" ?>&nbsp;en&nbsp;attente&nbsp;de&nbsp;signature<?php echo ($vracs['CONTRAT_EN_ATTENTE_SIGNATURE'] > 1) ? "s" : "" ?></p>
                        <?php $infos = false ?>
                    <?php endif; ?>
                    <?php if($vracs['CONTRAT_A_ENLEVER']): ?>
                        <p href="<?php echo url_for('mon_espace_civa_vrac', array('identifiant' => $compte->getIdentifiant())) ?>"><?php echo $vracs['CONTRAT_A_ENLEVER'] ?> contrat<?php echo ($vracs['CONTRAT_A_ENLEVER'] > 1) ? "s" : "" ?> <strong>à enlever</strong></p>
                        <?php $infos = false ?>
                    <?php endif; ?>
                    <?php if($infos): ?>
                    <p class="mineure">Aucune information à signaler</p>
                    <?php endif; ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_vrac', array('identifiant' => $compte->getIdentifiant())) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::GAMMA)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> gamma">
                <div class="bloc_acceuil_header">Alsace Gamm@</div>
                <div class="bloc_acceuil_content">
                    <p class="mineure">Aucune information à signaler</p>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_gamma') ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::DS_PROPRIETE)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> stocks">
                <div class="bloc_acceuil_header bloc_acceuil_header_deux_lignes" >Alsace stocks <br /><small style="font-size: 10px;">propriété</small></div>
                <div class="bloc_acceuil_content">
                    <?php if($sf_user->getDeclarantDS(DSCivaClient::TYPE_DS_PROPRIETE)->hasLieuxStockage() && $sf_user->isDsEditable(DSCivaClient::TYPE_DS_PROPRIETE) && (!$sf_user->getDs(DSCivaClient::TYPE_DS_PROPRIETE) || !$sf_user->getDs(DSCivaClient::TYPE_DS_PROPRIETE)->isValideeTiers())): ?>
                        <?php if(CurrentClient::getCurrent()->isDSDecembre()): ?>
                        <p><strong>A valider</strong> avant le 15/01/<?php echo CurrentClient::getCurrent()->getAnneeDS() + 1 ?></p>
                        <?php else: ?>
                        <p><strong>A valider</strong> avant le 10/09/<?php echo date('Y') ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mineure">Aucune information à signaler</p>
                    <?php endif; ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_ds', array("sf_subject" => DSCivaClient::getInstance()->getEtablissement($compte->getSociete(), DSCivaClient::TYPE_DS_PROPRIETE), "type" => DSCivaClient::TYPE_DS_PROPRIETE)) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::DS_NEGOCE)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> stocks">
                <div class="bloc_acceuil_header bloc_acceuil_header_deux_lignes">Alsace stocks <br /><small style="font-size: 10px;">négoce</small></div>
                <div class="bloc_acceuil_content">
                    <?php if($sf_user->isDsEditable(DSCivaClient::TYPE_DS_NEGOCE) && (!$sf_user->getDs(DSCivaClient::TYPE_DS_NEGOCE) || !$sf_user->getDs(DSCivaClient::TYPE_DS_NEGOCE)->isValideeTiers())): ?>
                        <?php if(CurrentClient::getCurrent()->isDSDecembre()): ?>
                        <p><strong>A valider</strong> avant le 15/01/<?php echo CurrentClient::getCurrent()->getAnneeDS() + 1 ?></p>
                        <?php else: ?>
                        <p><strong>A valider</strong> avant le 10/09/<?php echo date('Y') ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mineure">Aucune information à signaler</p>
                    <?php endif; ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_ds', array("sf_subject" => DSCivaClient::getInstance()->getEtablissement($compte->getSociete(), DSCivaClient::TYPE_DS_NEGOCE), "type" => DSCivaClient::TYPE_DS_NEGOCE)) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
