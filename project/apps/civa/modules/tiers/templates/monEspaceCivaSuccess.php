<?php use_helper('Date') ?>
<?php use_helper('Orthographe') ?>
<?php include_partial('tiers/onglets', array('active' => 'accueil', 'compte' => $compte, 'blocs' => $blocs)) ?>

<div id="application_dr" class="mon_espace_civa clearfix">

    <?php include_partial('tiers/title') ?>

    <div class="contenu">
        <?php if($sf_user->hasFlash('confirmation')) : ?>
            <p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
        <?php endif; ?>
        <h3 class="noir">Vos téléservices</h3>
        <div class="blocs_accueil_container_<?php echo $nb_blocs ?>">
            <?php $i = count($blocs) ?>
            <?php if ($compte->hasDroit(Roles::TELEDECLARATION_DR)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> recolte">
                <div class="bloc_acceuil_icon icon-raisins"></div>
                <div class="bloc_acceuil_header">Récolte</div>
                <div class="bloc_acceuil_content">
                    <?php if($drNeedToDeclare): ?>
                    <p><strong>À valider</strong> avant le 10 décembre minuit</p>
                    <?php elseif(date('Y-m-d') < DRClient::getInstance()->getDateOuverture()->format('Y-m-d')): ?>
                        <p class="mineure">Ouverture le <?php echo format_date(DRClient::getInstance()->getDateOuverture()->format('Y-m-d'), "dd MMMM", "fr_FR"); ?></p>
                    <?php else: ?>
                    <p class="mineure">Aucune information à signaler</p>
                    <?php endif; ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_dr_compte', $compte) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if ($compte->hasDroit(Roles::TELEDECLARATION_DR_ACHETEUR)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> recolte">
                <div class="bloc_acceuil_icon icon-raisins"></div>
                <div class="bloc_acceuil_header">Achat Récolte</div>
                <div class="bloc_acceuil_content">
                    <?php if(DRClient::getInstance()->isTeledeclarationOuverte()): ?>
                    <p>Le portail est <strong>ouvert</strong></p>
                    <?php else: ?>
                    <p class="mineure">Aucune information à signaler</p>
                    <?php endif; ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_dr_acheteur_compte', $compte) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php $sv = SVClient::getInstance()->findByIdentifiantAndCampagne(SVClient::getInstance()->getEtablissement($compte->getSociete())->identifiant, CurrentClient::getCurrent()->campagne); ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> recolte">
                <div class="bloc_acceuil_icon icon-raisins"></div>
                <div class="bloc_acceuil_header">Production</div>
                <div class="bloc_acceuil_content">
                    <?php if (SVClient::getInstance()->isTeledeclarationOuverte() && (!$sv || !$sv->isValide())): ?>
                        <p><strong>À valider</strong> avant le 9 janvier 2023 minuit</p>
                    <?php else: ?>
                        <p class="mineure">Aucune information à signaler</p>
                    <?php endif ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_production_compte', $compte) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if ($compte->hasDroit(Roles::TELEDECLARATION_DRM) && isset($blocs[Roles::TELEDECLARATION_DRM])): ?>
            <?php
            $date = date('Y-m-d');
            if(date('d') < 11){
                $date =  date('Y-m-d',strtotime($date." -1 month"));
            }
            $msg_drm = elision("Saisissez votre DRM de",format_date($date,'MMMM yyyy','fr_FR'));?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?>">
                <div class="bloc_acceuil_icon icon-vrac"></div>
                <div class="bloc_acceuil_header">DRM</div>
                <div class="bloc_acceuil_content">
                    <p <?php if(date('d') >= 11 ): ?>class="mineure"<?php endif; ?>><?php echo $msg_drm; ?></p>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo $blocs[Roles::TELEDECLARATION_DRM] ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if ($compte->hasDroit(Roles::TELEDECLARATION_VRAC) && isset($blocs[Roles::TELEDECLARATION_VRAC])): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> contrats">
                <div class="bloc_acceuil_icon icon-contrat2"></div>
                <div class="bloc_acceuil_header">Contrats</div>
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
                    <a href="<?php echo url_for('mon_espace_civa_vrac_compte', $compte) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if ($compte->hasDroit(Roles::TELEDECLARATION_GAMMA)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> gamma">
                <div class="bloc_acceuil_icon icon-camion"></div>
                <div class="bloc_acceuil_header">Gamm@</div>
                <div class="bloc_acceuil_content">
                    <p class="mineure">Aucune information à signaler</p>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for('mon_espace_civa_gamma_compte', $compte) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if ($compte->hasDroit(Roles::TELEDECLARATION_DS_PROPRIETE)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> stocks">
                <div class="bloc_acceuil_icon icon-stock"></div>
                <div class="bloc_acceuil_header bloc_acceuil_header_deux_lignes" >Stocks <br /><small style="font-size: 10px;">propriété</small></div>
                <div class="bloc_acceuil_content">
                    <?php if($sf_user->getDeclarantDS(DSCivaClient::TYPE_DS_PROPRIETE) && $sf_user->getDeclarantDS(DSCivaClient::TYPE_DS_PROPRIETE)->hasLieuxStockage() && DSCivaClient::getInstance()->isTeledeclarationOuverte() && (!$sf_user->getDs(DSCivaClient::TYPE_DS_PROPRIETE) || !$sf_user->getDs(DSCivaClient::TYPE_DS_PROPRIETE)->isValideeTiers())): ?>
                        <?php if(CurrentClient::getCurrent()->isDSDecembre()): ?>
                        <p class="mineure">Aucune information à signaler</p>
                        <!--<p><strong>A valider</strong> avant le 15/01/<?php echo CurrentClient::getCurrent()->getAnneeDS() + 1 ?></p>-->
                        <?php else: ?>
                        <p><strong>À valider</strong> avant le 10/09/<?php echo date('Y') ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mineure">Aucune information à signaler</p>
                    <?php endif; ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for("mon_espace_civa_ds_compte", array("type" => DSCivaClient::TYPE_DS_PROPRIETE, "sf_subject" => $compte)) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <?php if ($compte->hasDroit(Roles::TELEDECLARATION_DS_NEGOCE)): ?>
            <div class="bloc_acceuil <?php if($i == $nb_blocs): ?>bloc_acceuil_first<?php endif ?> <?php if($i == 1): ?>bloc_acceuil_last<?php endif; ?> <?php if(($nb_blocs - $i) % 2 == 1): ?>alt<?php endif ?> stocks">
                <div class="bloc_acceuil_icon icon-stock"></div>
                <div class="bloc_acceuil_header bloc_acceuil_header_deux_lignes">Stocks <br /><small style="font-size: 10px;">négoce</small></div>
                <div class="bloc_acceuil_content">
                    <?php if(DSCivaClient::getInstance()->isTeledeclarationOuverte() && (!$sf_user->getDs(DSCivaClient::TYPE_DS_NEGOCE) || !$sf_user->getDs(DSCivaClient::TYPE_DS_NEGOCE)->isValideeTiers())): ?>
                        <?php if(CurrentClient::getCurrent()->isDSDecembre()): ?>
                            <p class="mineure">Pas de stock au 31/12 cette année</p>
                            <!--<p><strong>A valider</strong> avant le 15/01/<?php echo CurrentClient::getCurrent()->getAnneeDS() + 1 ?></p>-->
                        <?php else: ?>
                        <p><strong>À valider</strong> avant le 10/09/<?php echo date('Y') ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mineure">Pas de stock au 31/12 cette année</p>
                    <?php endif; ?>
                </div>
                <div class="bloc_acceuil_footer">
                    <a href="<?php echo url_for("mon_espace_civa_ds_compte", array("type" => DSCivaClient::TYPE_DS_NEGOCE, "sf_subject" => $compte)) ?>">Accéder</a>
                </div>
            </div>
            <?php $i = $i -1 ?>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
