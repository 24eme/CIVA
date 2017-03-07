<form id="principal" action="" method="post">
	<h2 class="titre_principal">Important</h2>
	<div id="notice_evolutions">
		<h2>Nouveautés</h2>

		<ol style="font-size: 14px; padding-left: 5px;">
		    <li style="padding-top: 10px;">
		        Les rendements maximum de l’AOC Alsace et du Crémant d’Alsace ont été porté à <strong>83 hl/ha</strong>.<br /><br />
				<span style="padding-left: 20px;">ATTENTION, les rendements maximum Riesling, Pinot Gris et Gewuztraminer restent néanmoins à <strong>80 hl/ha</strong>.</span>
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
		<a href="<?php echo url_for("dr_telecharger_la_notice"); ?>" style="height: 20px;" class="telecharger-btn"></a>
		<br />
		<br />
		<br />
		<h2>Rappel</h2>
		<div style="font-size: 14px; padding-left: 5px;">
			<p>Depuis la récolte 2012, le volume à inscrire en entrée est le <u>volume total récolté</u>, c’est-à-dire <u>lies et bourbes comprises</u> (même si des soutirages ont déjà été effectués).</p>

			<p>La rubrique "Volume à détruire" comprend à la fois les lies et les volumes éventuels en dépassement de rendement (sans distinction).</p>

			<p>A compter de cette année, la gestion des lies doit se faire obligatoirement par cépage. Vous inscrivez vos lies connues dans la ligne "Volume à détruire" et le système vous calculera automatiquement le volume revendiqué par cépage.</p>
		</div>
	</div>
	<?php include_partial('dr/boutons', array('display' => array('precedent','suivant'), 'dr' => $dr)) ?>
</form>
