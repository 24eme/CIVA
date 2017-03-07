<div id="nouvelle_declaration">
<?php if (DRSecurity::getInstance($dr, $etablissement)->isAuthorized(DRSecurity::EDITION)): ?>
	<?php include_component('dr', 'monEspaceEnCours', array('dr' => $dr, 'etablissement' => $etablissement, 'campagne' => $campagne)) ?>
<?php elseif ($dr && $dr->isValideeTiers()): ?>
    <?php include_partial('dr/monEspaceValidee', array('dr' => $dr)) ?>
<?php elseif(date('Y-m-d') < DRClient::getInstance()->getDateOuverture()->format('Y-m-d')): ?>
    <?php include_partial('dr/monEspaceNonOuverte', array('dr' => $dr)) ?>
<?php else: ?>
    <?php include_partial('dr/monEspaceNonEditable', array('dr' => $dr)) ?>
<?php endif; ?>
</div>
