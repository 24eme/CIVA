<!--<ul id="liens_evitement" class="clearfix">
</ul>-->

<!-- #header -->
<div id="header" class="clearfix pngfix">
    <h1 id="logo"><a href="<?php echo url_for('@mon_espace_civa'); ?>" title="CIVA - Conseil Interprofessionnel des Vins d'Alsace - Retour à l'accueil"><img src="/images/visuels/logo_civa.png" alt="CIVA - Conseil Interprofessionnel des Vins d'Alsace" /></a></h1>

    <div id="titre_rubrique">
        <?php $title = $sf_context->getInstance()->getResponse()->getTitle(); ?>

        <h1>
            <?php if (strrpos($title,' - ') !== false) :?>
                <?php printf(html_entity_decode(substr($title,strrpos($title,'-')+1,strlen($title))) , $sf_request->getParameter('annee', date("Y")));?>
            <?php else: ?>
                <?php printf(html_entity_decode($title), $sf_request->getParameter('annee', date("Y")));?>
            <?php endif; ?>
        </h1>
        <?php if ($sf_user->isDeclarant() || $sf_user->isNonDeclarant()) : ?>
            <p class="utilisateur"><?php echo link_to($sf_user->getTiers()->getIntitule().' '.$sf_user->getTiers()->getNom(), '@mon_espace_civa'); ?></p>
        <?php endif; ?>
    </div>

    <div id="acces_directs">
        <h2>Accès directs</h2>
        <ul>
            <?php if ($sf_user->isDeclarant()): ?>
                <li><a href="<?php echo url_for('@mon_espace_civa'); ?>">Ma déclaration</a></li>
            <?php endif; ?>
            <?php if ($sf_user->isNonDeclarant()) : ?>
                <li><a href="<?php echo url_for('@mon_espace_civa_non_declarant'); ?>">Mon espace civa</a></li>
            <?php endif; ?>
            <?php if (($sf_user->isDeclarant() || $sf_user->isNonDeclarant()) && !$sf_user->isFictif()) : ?>
                <li><a href="<?php echo url_for('@mon_compte'); ?>">Mon compte</a></li>
            <?php endif; ?>
            <?php if ($sf_user->isAdmin()) : ?>
                <li class="admin"><a href="<?php echo url_for('@login_admin'); ?>">Administration</a></li>
            <?php endif; ?>

            <?php if($sf_user->isAuthenticated()): ?>
                <li><a href="<?php echo url_for('@logout'); ?>">Deconnexion</a></li>
            <?php else : ?>
                <li><a href="<?php echo url_for('@login'); ?>">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- fin #header -->