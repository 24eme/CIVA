<ul id="onglets_majeurs" class="clearfix">
	<?php if(count(TiersSecurity::getInstance($compte)->getBlocs()) > 1): ?>
	<li class="<?php if($active== 'accueil'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa", array("identifiant" => $compte->getIdentifiant())) ?>">Accueil</a></li>
	<?php endif; ?>
	<?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::DR)): ?>
	<li class="<?php if($active== 'recolte'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_dr", DRClient::getInstance()->getEtablissement($compte->getSociete())) ?>">Récolte</a></li>
	<?php endif; ?>
	<?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::DR_ACHETEUR)): ?>
	<li class="<?php if($active== 'recolte_acheteur'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_dr_acheteur") ?>">Acheteur Récolte</a></li>
	<?php endif; ?>
	<li class=""><a href="<?php echo url_for("drm_etablissement", array("identifiant" => $compte->getIdentifiant())) ?>">DRM</a></li>
	<?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::VRAC)): ?>
	<li class="<?php if($active== 'vrac'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_vrac") ?>">Contrats</a></li>
	<?php endif; ?>
	<?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::GAMMA)): ?>
	<li class="<?php if($active== 'gamma'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_gamma") ?>">Gamm@</a></li>
	<?php endif; ?>
	<?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::DS_PROPRIETE)): ?>
	<li class="<?php if($active== 'stock_'.DSCivaClient::TYPE_DS_PROPRIETE): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_ds", array("type" => DSCivaClient::TYPE_DS_PROPRIETE, "sf_subject" => DSCivaClient::getInstance()->getEtablissement($compte->getSociete(), DSCivaClient::TYPE_DS_PROPRIETE))) ?>">Stocks Propriété</a></li>
	<?php endif; ?>
	<?php if (TiersSecurity::getInstance($compte)->isAuthorized(TiersSecurity::DS_NEGOCE)): ?>
	<li class="<?php if($active== 'stock_'.DSCivaClient::TYPE_DS_NEGOCE): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_ds", array("type" => DSCivaClient::TYPE_DS_NEGOCE, "sf_subject" => DSCivaClient::getInstance()->getEtablissement($compte->getSociete(), DSCivaClient::TYPE_DS_NEGOCE))) ?>">Stocks Négoce</a></li>
	<?php endif; ?>
</ul>
