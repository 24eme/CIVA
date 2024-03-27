<?php if(!isset($title)): ?>
    <?php $title = $sf_context->getInstance()->getResponse()->getTitle(); ?>
    <?php if(acCouchdbManager::getClient("Current")->hasCurrentFromTheFuture()): ?>
        <?php $title="Hey ! Marty ! Nous sommes en ".CurrentClient::getInstance()->getCurrent()->getCampagne()." !"; ?>
    <?php endif; ?>
<?php endif; ?>
<div id="header" class="clearfix pngfix">
    <h1 id="logo"><a href="<?php if($compte): ?><?php echo url_for('mon_espace_civa', array('identifiant' => $compte->getLogin()), isset($isAbsoluteUrl)); ?><?php endif; ?>" title="CIVA - Conseil Interprofessionnel des Vins d'Alsace - Retour à l'accueil"><img src="<?php echo image_path("/images/visuels/logo_civa.png", true) ?>" alt="CIVA - Conseil Interprofessionnel des Vins d'Alsace" /></a></h1>

    <div id="titre_rubrique">
        <h1>
            <?php if (strrpos($title,' - ') !== false) :?>
                <?php printf(html_entity_decode(substr($title,strrpos($title,'-')+1,strlen($title))) , $sf_request->getParameter('annee', date("Y")));?>
            <?php else: ?>
                <?php printf(html_entity_decode($title), $sf_request->getParameter('annee', date("Y")));?>
            <?php endif; ?>
        </h1>
        <?php if ($compte) : ?>
            <p class="utilisateur">
                <?php if ($compteOrigine):?>
                        <?php echo sprintf('%s , vous êtes connecté en tant que %s', $compteOrigine->getNomAAfficher(), $compte->getNomAAfficher()) ;?>
                <?php else : ?>
                        <?php echo link_to($compte->getNomAAfficher(), 'tiers');  ?>
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>

    <div id="acces_directs">
        <h2>Accès directs</h2>
        <ul>
            <?php if ($compte && $compte->getRawValue() instanceof Compte): ?>
                <li><a href="<?php echo url_for('mon_espace_civa', array('identifiant' => $compte->getIdentifiant()), isset($isAbsoluteUrl)); ?>">Mes déclarations</a></li>
            <?php endif; ?>
            <li><a href="http://vinsalsace.pro/">Mon espace CIVA</a></li>
            <li><a href="http://declaration.ava-aoc.fr">Mon espace AVA</a></li>

            <?php if ($compte) : ?>
                <li><a href="<?php echo url_for('compte_modification', ['identifiant' => $compte->login], isset($isAbsoluteUrl)); ?>">Mon compte</a></li>
            <?php endif; ?>

            <?php if($compte): ?>
                <?php if ($isAdmin): ?>
                    <li class="admin"><a href="<?php echo url_for('admin', array(), isset($isAbsoluteUrl)); ?>">Administration</a></li>
                <?php elseif($compteOrigine):?>
                     <li class="red"><a href="<?php echo url_for('delegate_mode_retour_espace_civa', array(), isset($isAbsoluteUrl)); ?>">Retour à mon espace</a></li>
                <?php endif; ?>
                <?php if(!$compteOrigine) : ?>
                     <li><a href="<?php echo url_for('logout', array(), isset($isAbsoluteUrl)); ?>">Déconnexion</a></li>
                <?php endif; ?>
            <?php else : ?>
                <li><a href="<?php echo url_for('login', array(), isset($isAbsoluteUrl)); ?>">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- fin #header -->
