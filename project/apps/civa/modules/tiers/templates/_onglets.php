<ul id="onglets_majeurs" class="clearfix">
	<?php if(count($blocs) != 1): ?>
	<li class="<?php if($active== 'accueil'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa", array("identifiant" => $compte->getIdentifiant())) ?>">Accueil</a></li>
	<?php endif ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DR)): ?>
	<li class="<?php if($active== 'recolte'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_dr_compte", $compte) ?>">Récolte</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DR_ACHETEUR)): ?>
	<li class="<?php if($active== 'recolte_acheteur'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_dr_acheteur_compte", $compte) ?>">Achat Récolte</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DRM) && isset($blocs[Roles::TELEDECLARATION_DRM])): ?>
 	<li class=""><a href="<?php echo $blocs[Roles::TELEDECLARATION_DRM] ?>">DRM</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_VRAC) && isset($blocs[Roles::TELEDECLARATION_VRAC])): ?>
	<li class="<?php if($active== 'vrac'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_vrac_compte", $compte) ?>">Contrats</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_GAMMA)): ?>
	<li class="<?php if($active== 'gamma'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_gamma_compte", $compte) ?>">Gamm@</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DS_PROPRIETE)): ?>
	<li class="<?php if($active== 'stock_'.DSCivaClient::TYPE_DS_PROPRIETE): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_ds_compte", array("type" => DSCivaClient::TYPE_DS_PROPRIETE, "sf_subject" => $compte)) ?>">Stocks Propriété</a></li>
	<?php endif; ?>
	<?php if ($compte->hasDroit(Roles::TELEDECLARATION_DS_NEGOCE)): ?>
	<li class="<?php if($active== 'stock_'.DSCivaClient::TYPE_DS_NEGOCE): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_ds_compte", array("type" => DSCivaClient::TYPE_DS_NEGOCE, "sf_subject" => $compte)) ?>">Stocks Négoce</a></li>
	<?php endif; ?>
</ul>
