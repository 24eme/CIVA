<?php $etablissement = $sv->getEtablissement() ?>
<?php $compte = $etablissement->getMasterCompte() ?>
<ol class="breadcrumb">
    <li><a href="<?php echo url_for("mon_espace_civa_production_compte", $compte) ?>">Déclaration de production</a></li>
    <li><a href="<?php echo url_for("mon_espace_civa_production_compte", $compte) ?>"><?php echo $compte->nom_a_afficher ?></a></li>
    <li><a class="active" href=""><?php echo $sv->type ?> <?php echo $sv->campagne; ?></a></li>
</ol>