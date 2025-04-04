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
<?php if ($vrac->isValide()): ?>
    <a class="btn_majeur btn_jaune" href="<?php echo url_for('vrac_fiche', ['sf_subject' => $vrac]);  ?>">
        Quitter la modification
        <svg style="vertical-align: -.125em;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
          <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
        </svg>
    </a>
<?php endif; ?>
<?php if(VracSecurity::getInstance($sf_user->getCompte(), $vrac)->isAuthorized(VracSecurity::SUPPRESSION)): ?>
	<a style="padding-left: 30px;" class="btn_majeur btn_noir" href="<?php echo url_for('vrac_supprimer', $vrac) ?>">
		<svg style="position: absolute; left: 10px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg> Supprimer
	</a>
<?php endif; ?>
</div>
</div>
<?php endif; ?>
