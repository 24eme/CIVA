<ul id="liens_evitement" class="clearfix">
</ul>

<!-- #header -->
<div id="header" class="clearfix pngfix">
    <h1 id="logo"><a href="<?php echo url_for('@mon_espace_civa'); ?>" title="CIVA - Conseil Interprofessionnel des Vins d'Alsace - Retour à l'accueil"><img src="/images/visuels/logo_civa.png" alt="CIVA - Conseil Interprofessionnel des Vins d'Alsace" /></a></h1>

    <div id="titre_rubrique">
        <h1>Déclaration de récolte de vins d'Alsace 2010</h1>
<?php if ($recoltant = $sf_user->getRecoltant()) : ?>
        <p class="utilisateur"><?php echo link_to($recoltant->getExploitant()->getNom(), '@mon_espace_civa'); ?></a></p>
        <p class="domaine"><?php echo $recoltant->getNom();?></p>
<?php endif; ?>
    </div>

    <div id="acces_directs">
        <h2>Accès directs</h2>
        <ul>
<?php if ($recoltant = $sf_user->getRecoltant()) : ?>
            <li><a href="<?php echo url_for('@mon_espace_civa'); ?>">Mon compte</a></li>
            <li><a href="<?php echo url_for('@logout'); ?>">Deconnexion</a></li>
<?php else : ?>
            <li><a href="<?php echo url_for('@login'); ?>">Connexion</a></li>

<?php endif; ?>
        </ul>
    </div>
</div>
<!-- fin #header -->