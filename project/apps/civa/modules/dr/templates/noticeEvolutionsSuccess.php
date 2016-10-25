<form id="principal" action="" method="post">
	<h2 class="titre_principal">Important</h2>
	<div id="notice_evolutions">
		<h2>Nouveautés</h2>

		<ol style="font-size: 14px; padding-left: 5px;">
		    <li style="padding-top: 10px;">
		        Les rendements maximum de l’AOC Alsace et du Crémant d’Alsace ont été porté à 83 hl/ha.<br /><br />
				<span style="padding-left: 20px;">ATTENTION, les rendements maximum Riesling, Pinot Gris et Gewuztraminer restent néanmoins à 80 hl/ha</span>
		    </li>
		    <li style="padding-top: 10px;">
				Un rendement spécifique est mis en place à compter de cette année pour les mentions VT et SGN :<br />
		        <ul style="list-style-type: circle; list-style-position: inside; padding-left: 20px; padding-top: 10px;">
		            <li>Vendanges Tardives : <strong>55 hl/ha</strong></li>
		            <li>Sélection de Grains Nobles : <strong>40 hl/ha</strong></li>
		        </ul>
				<br /><span style="padding-left: 20px;">Le calcul du rendement se fait par Appellation/Cépage/Mention.</span>
		    </li>
		</ol>
		<br />
		<br />
		<a href="<?php echo url_for("dr_telecharger_la_notice"); ?>" style="height: 20px; display: none;" class="telecharger-btn"></a>
	</div>
	<?php include_partial('dr/boutons', array('display' => array('precedent','suivant'), 'dr' => $dr)) ?>
</form>
