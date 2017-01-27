<!--<ul id="liens_evitement" class="clearfix">
</ul>-->

<!-- #header -->
<div id="header" class="clearfix pngfix">
    <h1 id="logo"><a href="<?php if($sf_user->isAuthenticated()): ?><?php echo url_for('mon_espace_civa', array('identifiant' => $sf_user->getCompte()->getLogin())); ?><?php endif; ?>" title="CIVA - Conseil Interprofessionnel des Vins d'Alsace - Retour à l'accueil"><img src="/images/visuels/logo_civa.png" alt="CIVA - Conseil Interprofessionnel des Vins d'Alsace" /></a></h1>

    <div id="titre_rubrique">
        <?php $title = $sf_context->getInstance()->getResponse()->getTitle(); ?>

        <?php if(acCouchdbManager::getClient("Current")->hasCurrentFromTheFuture()): ?>
            <?php $title="Hey ! Marty ! Nous sommes en ".CurrentClient::getInstance()->getCurrent()->getCampagne()." !"; ?>
        <?php endif; ?>

        <h1>
            <?php if (strrpos($title,' - ') !== false) :?>
                <?php printf(html_entity_decode(substr($title,strrpos($title,'-')+1,strlen($title))) , $sf_request->getParameter('annee', date("Y")));?>
            <?php else: ?>
                <?php printf(html_entity_decode($title), $sf_request->getParameter('annee', date("Y")));?>
            <?php endif; ?>
        </h1>
        <?php if ($sf_user->hasCredential('tiers')) : ?>
            <p class="utilisateur">
                <?php if ($sf_user->isInDelegateMode()):?>
                        <?php echo sprintf('%s , vous êtes connecté en tant que %s', $sf_user->getCompte(myUser::NAMESPACE_COMPTE_AUTHENTICATED)->getNomAAfficher(), $sf_user->getCompte()->getNomAAfficher()) ;?>
                <?php else : ?>
                        <?php echo link_to($sf_user->getCompte()->getNomAAfficher(), 'tiers');  ?>
                <?php endif; ?>
            </p>
        <?php elseif ($sf_user->hasCredential('compte')) : ?>
            <p class="utilisateur"><?php echo link_to($sf_user->getCompte()->getNomAAfficher(), 'tiers'); ?></p>
        <?php endif; ?>
    </div>

    <div id="acces_directs">
        <h2>Accès directs</h2>
        <ul>
            <?php if ($sf_user->hasCredential('tiers')): ?>
                <li><a href="<?php echo url_for('mon_espace_civa', array('identifiant' => $sf_user->getCompte()->getIdentifiant())); ?>">Mes déclarations</a></li>
                <li><a href="http://vinsalsace.pro/">Mon espace CIVA</a></li>
                <li><a href="http://declaration.ava-aoc.fr">Mon espace AVA</a></li>
            <?php else: ?>
                <li><a href="http://vinsalsace.pro/">Mon espace CIVA</a></li>
                <li><a href="http://declaration.ava-aoc.fr">Mon espace AVA</a></li>
            <?php endif; ?>
            <?php if ($sf_user->hasCredential('compte') && $sf_user->getCompte()->getStatus() == CompteClient::STATUT_TELEDECLARANT_INSCRIT) : ?>
                <li><a href="<?php echo url_for('@compte_modification'); ?>">Mon compte</a></li>
            <?php elseif($sf_user->hasCredential('compte') && $sf_user->getCompte()->getStatus() == CompteClient::STATUT_TELEDECLARANT_OUBLIE): ?>
                <li><a href="<?php echo url_for('@compte_modification_oublie'); ?>">Mon compte</a></li>
            <?php endif; ?>

            <?php if($sf_user->isAuthenticated()): ?>
                <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR)): ?>
                    <li class="admin"><a href="<?php echo url_for('@admin'); ?>">Administration</a></li>
                <?php elseif($sf_user->isInDelegateMode()):?>
                     <li class="red"><a href="<?php echo url_for('@delegate_mode_retour_espace_civa'); ?>">Retour à mon espace</a></li>
                <?php endif; ?>
                <?php if(!$sf_user->isInDelegateMode()) : ?>
                     <li><a href="<?php echo url_for('@logout'); ?>">Déconnexion</a></li>
                <?php endif; ?>
            <?php else : ?>
                <li><a href="<?php echo url_for('@login'); ?>">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- fin #header -->
