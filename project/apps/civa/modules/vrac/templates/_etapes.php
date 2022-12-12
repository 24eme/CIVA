<?php if ($etapes->getNbEtape() > 0): ?>
<div class="header_ds clearfix">
<ul id="etape_declaration" class="etapes_ds clearfix">
	<?php foreach ($etapes->getEtapes() as $etape => $position): ?>
	<li class="<?php echo ($etape == $current)? 'actif ' : ''; echo ($etapes->isLt($etape, $vrac->etape))? 'passe' : ''; ?>">
		<?php if($etapes->isLt($etape, $vrac->etape)): ?>
		<a href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'etape' => $etape)) ?>"><span><?php echo $etapes->getLibelle($etape) ?></span> <em>Etape <?php echo $position ?></em></a>
		<?php elseif (($etape == $current)): ?>
		<a href="#"><span><?php echo $etapes->getLibelle($etape) ?></span> <em>Etape <?php echo $position ?></em></a>
		<?php else: ?>
		<span><?php echo $etapes->getLibelle($etape) ?></span> <em>Etape <?php echo $position ?></em>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>
<div id="suppression_contrat">
<?php if(VracSecurity::getInstance($sf_user->getCompte(), $vrac)->isAuthorized(VracSecurity::SUPPRESSION)): ?>
	<a id="btn_precedent" href="<?php echo url_for('vrac_supprimer', $vrac) ?>">
        Supprimer
        <svg style="color:white;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trash" viewBox="0 -1 16 16">
          <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
          <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
        </svg>
	</a>
<?php endif; ?>
</div>
</div>
<?php endif; ?>
