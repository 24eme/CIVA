<?php if ($etapes->getNbEtape() > 0): ?>
<ol>
	<?php foreach ($etapes->getEtapes() as $etape => $position): ?>
	<li>
		<?php if ($etape == $current): ?>
		<strong><?php echo $etapes->getLibelle($etape) ?></strong>
		<?php elseif($etapes->isLt($etape, $etapes->getPrev($vrac->etape))): ?>
		<a href="<?php echo url_for('vrac_etape_'.$etape, $vrac) ?>"><?php echo $etapes->getLibelle($etape) ?></a>
		<?php else: ?>
		<?php echo $etapes->getLibelle($etape) ?>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ol>
<?php endif; ?>