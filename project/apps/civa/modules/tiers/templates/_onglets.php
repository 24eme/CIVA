<ul id="onglets_majeurs" class="clearfix">
	<?php if(count($blocs) != 1): ?>
	<li class="<?php if($active== 'accueil'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa", array("identifiant" => $compte->getIdentifiant()), isset($absolute)) ?>">Accueil</a></li>
	<?php endif ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DR)): ?>
	<li class="<?php if($active== 'recolte'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_dr_compte", $compte, isset($absolute)) ?>">Récolte</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DR_ACHETEUR)): ?>
	<li class="<?php if($active== 'recolte_acheteur'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_dr_acheteur_compte", $compte, isset($absolute)) ?>">Achat Récolte</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DRM) && isset($blocs[Roles::TELEDECLARATION_DRM])): ?>
 	<li class="<?php if($active== 'drm'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo $blocs[Roles::TELEDECLARATION_DRM] ?>">DRM</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_VRAC) && isset($blocs[Roles::TELEDECLARATION_VRAC])): ?>
	<li class="<?php if($active== 'vrac'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_vrac_compte", $compte, isset($absolute)) ?>">Contrats</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_GAMMA)): ?>
	<li class="<?php if($active== 'gamma'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_gamma_compte", $compte, isset($absolute)) ?>">Gamm@</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DS_PROPRIETE)): ?>
	<li class="<?php if($active== 'stock_'.DSCivaClient::TYPE_DS_PROPRIETE): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_ds_compte", array("type" => DSCivaClient::TYPE_DS_PROPRIETE, "sf_subject" => $compte), isset($absolute)) ?>">Stocks Propriété</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DS_NEGOCE)): ?>
	<li class="<?php if($active== 'stock_'.DSCivaClient::TYPE_DS_NEGOCE): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_ds_compte", array("type" => DSCivaClient::TYPE_DS_NEGOCE, "sf_subject" => $compte), isset($absolute)) ?>">Stocks Négoce</a></li>
	<?php endif; ?>
</ul>
