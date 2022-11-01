<form id="principal" action="" method="post">
	<h2 class="titre_principal">Important</h2>
	<div id="notice_evolutions">
		<h2>Rendements 2022</h2>
		<p style="font-size: 14px; margin-top: 15px;">Depuis la Récolte 2020 les rendements de l'AOC Alsace blanc sont calculés <strong>PAR CÉPAGE</strong>, avec cette année encore un rendement différencié entre les cépages. (pour plus de détails vous pouvez consulter la Notice d'aide en ligne).</p>
        <p style="font-size: 14px; margin-top: 15px;">Par conséquent pour déclarer de l'Assemblage (Edel), il est donc <strong>OBLIGATOIRE</strong> de déclarer une surface prise en déduction des surfaces des autres cépages blancs.</p>
        <p style="font-size: 14px; margin-top: 15px;">Les  rendements des AOC Communale et Lieu-dit sont cette année également calculés <strong>PAR CÉPAGE</strong>.</p>
		<br />
		<h2>Constitution de VCI pour la Récolte 2021</h2>
		<p style="font-size: 14px; margin-top: 15px;">Appellations :<br />
			<ul style="font-size: 14px; padding-left: 20px; list-style-type: initial;">
			    <li style="padding-top: 5px;">
					AOC Alsace : 8 hl/ha <strong>PAR CÉPAGE</strong>
			    </li>
			    <li style="padding-top: 5px;">
					AOC Crémant d’Alsace : 8 hl/ha pour l'Appellation, à constituer dans les différents cépages
			    </li>
			</ul>
		</p>
		<p style="font-size: 14px; margin-top: 10px;">Pour plus de précisions lire le <u><a href="<?php echo url_for('dr_telecharger_guide_vci') ?>">guide du VCI</a></u> dans la rubrique 'Documents d'Aide'</p>
		<br />
		<h2>Rappels</h2>
		<p style="font-size: 14px; margin-top: 15px;">Depuis la Récolte 2018, la production de Jus de Raisin doit obligatoirement figurer sur la Déclaration de Récolte Alsace dans la rubrique 'Récolte Autres'.</p>
		<p style="font-size: 14px; margin-top: 5px;">Les surfaces déclarées en production de jus de raisin viendront en déduction de la surface déclarée en AOC.</p>
		<p style="font-size: 14px; margin-top: 5px;">Il n'existe pas de rendement maximum pour ce produit, la préconisation est de respecter un rendement agronomique réaliste.</p>
		<br />

		<div style="font-size: 14px; padding-left: 5px;">
			<p style="font-size: 14px; margin-top: 15px;">Depuis la récolte 2016, un rendement spécifique est mis en place pour les mentions VT et SGN (le calcul du rendement se fait par Appellation/Cépage/Mention) :<br />
				<ul style="font-size: 14px; padding-left: 20px; list-style-type: initial;">
				    <li style="padding-top: 5px;">
						Vendanges Tardives : 55 hl/ha <strong>(Attention 50 hl/ha pour l'AOC Grand Cru, ainsi que pour le GW Lieu-dit)</strong>
				    </li>
				    <li style="padding-top:5px;">
						Sélection de Grains Nobles : 40 hl/ha
				    </li>
				</ul>
			</p>
			<br />

			<p>Depuis la récolte 2012, le volume à inscrire en entrée est le <u><strong>volume total récolté</strong></u>, c’est-à-dire <u>lies et bourbes comprises</u> (même si des soutirages ont déjà été effectués, voire livrés en distillerie).</p>

			<p>La rubrique "Volume à détruire" comprend à la fois les lies et les volumes éventuels en dépassement de rendement (sans distinction).</p>

			<br />

			<p>Vous inscrivez vos lies connues dans la ligne "Volume à détruire" et le système vous calculera automatiquement le volume revendiqué par cépage.</p>
			<br />
			<br />
			<a href="<?php echo url_for("dr_telecharger_la_notice"); ?>" style="height: 20px;" class="telecharger-btn"></a>
		</div>
	</div>
	<?php include_partial('dr/boutons', array('display' => array('precedent','suivant'), 'dr' => $dr)) ?>
</form>
