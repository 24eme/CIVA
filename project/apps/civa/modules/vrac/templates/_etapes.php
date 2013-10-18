<?php if ($etapes->getNbEtape() > 0): ?>
<div class="header_ds clearfix">
<ul id="etape_declaration" class="etapes_ds clearfix">
	<?php foreach ($etapes->getEtapes() as $etape => $position): ?>
	<li class="<?php echo ($etape == $current)? 'actif ' : ''; echo ($etapes->isLt($etape, $vrac->etape))? 'passe' : ''; ?>">
		<?php if($etapes->isLt($etape, $vrac->etape) || $etape == $vrac->etape): ?>
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
<?php if ($vrac->isSupprimable($user->_id)): ?>
	<a id="btn_precedent" href="<?php echo url_for('vrac_supprimer', $vrac) ?>">
		<img alt="Retourner à l'étape précédente" src="/images/boutons/btn_supprimer_contrat.png">
	</a>
<?php endif; ?>
</div>
</div>
<?php endif; ?>