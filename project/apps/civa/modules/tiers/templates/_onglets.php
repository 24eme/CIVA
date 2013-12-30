<ul id="onglets_majeurs" class="clearfix">
	<li class="<?php if($active== 'accueil'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa") ?>">Accueil</a></li>
	<?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DR)): ?>
	<li class="<?php if($active== 'recolte'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_dr") ?>">Alsace Récolte</a></li>
	<?php endif; ?>
	<?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DR_APPORTEUR)): ?>
	<li class="<?php if($active== 'recolte_apporteur'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_dr_apporteur") ?>">Alsace Récolte</a></li>
	<?php endif; ?>
	<?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::VRAC)): ?>
	<li class="<?php if($active== 'vrac'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_vrac") ?>">Alsace Contrats</a></li>
	<?php endif; ?>
	<?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::GAMMA)): ?>
	<li class="<?php if($active== 'gamma'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_gamma") ?>">Alsace Gamm@</a></li>
	<?php endif; ?>
	<?php if (TiersSecurity::getInstance($sf_user)->isAuthorized(TiersSecurity::DS)): ?>
	<li class="<?php if($active== 'stock'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("mon_espace_civa_ds") ?>">Alsace Stocks</a></li>
	<?php endif; ?>
</ul>