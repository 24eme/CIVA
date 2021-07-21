<nav id="main_nav">
<ul id="onglets_majeurs" class="clearfix">
	<li class="<?php if($active=='accueil'): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo url_for("admin", array(), isset($absolute)) ?>">Admin</a></li>
	<li><a href="<?php echo url_for("admin", array(), isset($absolute)) ?>">Récolte</a></li>
	<li><a href="<?php echo url_for("admin", array(), isset($absolute)) ?>">Achat Récolte</a></li>
	<li><a href="<?php echo url_for("admin", array(), isset($absolute)) ?>">DRM</a></li>
	<li><a href="<?php echo url_for("admin", array(), isset($absolute)) ?>">Contrats</a></li>
	<li><a href="<?php echo url_for("admin", array(), isset($absolute)) ?>">Gamm@</a></li>
	<li><a href="<?php echo url_for("admin", array(), isset($absolute)) ?>">Stocks Propriété</a></li>
	<li><a href="<?php echo url_for("admin", array(), isset($absolute)) ?>">Stocks Négoce</a></li>
	<?php if(sfConfig::get("app_giilda_url_facture_admin")): ?>
	<li class="<?php if(preg_match('/(facture)/', $active)): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo sfConfig::get("app_giilda_url_facture_admin") ?>">Factures</a></li>
	<?php endif; ?>
	<?php if(sfConfig::get("app_giilda_url_facture_admin")): ?>
	<li class="<?php if(preg_match('/(societe|etablissement|compte)/', $active)): ?>ui-tabs-selected<?php endif; ?>"><a href="<?php echo sfConfig::get("app_giilda_url_compte_admin") ?>">Contacts</a></li>
	<?php endif; ?>
</ul>
</nav>
