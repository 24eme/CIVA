<?php if ($etapes->getNbEtape() > 0): ?>
<div class="header_ds clearfix">
<ul id="etape_declaration" class="etapes_ds clearfix">
	<?php foreach ($etapes->getEtapes() as $etape => $position): ?>
	<li class="<?php echo ($etape == $current)? 'actif ' : ''; echo ($etapes->isLt($etape, $etapes->getPrev($vrac->etape)))? 'passe' : ''; ?>">
		<?php if($etapes->isLt($etape, $etapes->getPrev($vrac->etape))): ?>
		<a href="<?php echo url_for('vrac_etape_'.$etape, $vrac) ?>"><span><?php echo $etapes->getLibelle($etape) ?></span> <em>Etape <?php echo $position ?></em></a>
		<?php elseif (($etape == $current)): ?>
		<a href="#"><span><?php echo $etapes->getLibelle($etape) ?></span> <em>Etape <?php echo $position ?></em></a>
		<?php else: ?>
		<span><?php echo $etapes->getLibelle($etape) ?></span> <em>Etape <?php echo $position ?></em>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>