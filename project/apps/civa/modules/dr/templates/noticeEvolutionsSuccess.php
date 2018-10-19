<form id="principal" action="" method="post">
	<h2 class="titre_principal">Important</h2>
	<div id="notice_evolutions">
		<h2><strong>NOUVEAU</strong> : Jus de Raisin</h2>
		<p style="font-size: 14px; margin-top: 15px;">À compter de la Récolte 2018, la production de Jus de Raisin doit obligatoirement figurer sur la Déclaration de Récolte Alsace dans la rubrique 'Récolte Autres'.</p>
		<p style="font-size: 14px; margin-top: 5px;">Les surfaces déclarées en production de jus de raisin viendront en déduction de la surface déclarée en AOC.</p>
		<p style="font-size: 14px; margin-top: 5px;">Il n'existe pas de rendement maximum pour ce produit, la préconisation serait de respecter un rendement agronomique réaliste.</p>
		<br />
		<h2>Reconduction de la constitution de VCI pour la Récolte 2018</h2>
		<p style="font-size: 14px; margin-top: 15px;">Appellations concernées :<br />
			<ul style="font-size: 14px; padding-left: 20px; list-style-type: initial;">
			    <li style="padding-top: 5px;">
			       AOC « Crémant d’Alsace »
			    </li>
			    <li style="padding-top: 5px;">
					AOC « Alsace » uniquement pour les vins blancs à l’exception des dénominations géographiques complémentaires (communales), des lieux-dits et des mentions « Vendanges Tardives » et « Sélection de Grains Nobles ».
 Les vins rouges et rosés, et les AOC « Alsace Grand cru – lieu-dit » sont exclus de ce dispositif.
			    </li>
			</ul>
		</p>
		<p style="font-size: 14px; margin-top: 10px;">Limite de production :<br />
			<ul style="font-size: 14px; padding-left: 20px; list-style-type: initial;">
			    <li style="padding-top: 5px;">
			      	5hl/ha par appellation concernée
			    </li>
			    <li style="padding-top: 5px;">
					dans la limite des rendements maximum par cépage pour l’AOC « Alsace »
			    </li>
			</ul>
		</p>
		<p style="font-size: 14px; margin-top: 10px;">Pour plus de précisions lire le <u><a href="<?php echo url_for('dr_telecharger_guide_vci') ?>">guide du VCI</a></u> dans la rubrique 'Documents d'Aide'</p>
		<br />
		<h2>Rappel</h2>
		<div style="font-size: 14px; padding-left: 5px;">
			<p style="font-size: 14px; margin-top: 15px;">Depuis la récolte 2016, un rendement spécifique est mis en place pour les mentions VT et SGN :<br />
				<ul style="font-size: 14px; padding-left: 20px; list-style-type: initial;">
				    <li style="padding-top: 5px;">
				      	Vendanges Tardives : <strong>55 hl/ha</strong>
				    </li>
				    <li style="padding-top:5px;">
						Sélection de Grains Nobles : <strong>40 hl/ha</strong>
				    </li>
				</ul>
			</p>
			<p>Le calcul du rendement se fait par Appellation/Cépage/Mention.</p>
			<p style="margin-top: 10px;">Depuis la récolte 2012, le volume à inscrire en entrée est le <u>volume total récolté</u>, c’est-à-dire <u>lies et bourbes comprises</u> (même si des soutirages ont déjà été effectués), voire livrés en distillerie).</p>

			<p>La rubrique "Volume à détruire" comprend à la fois les lies et les volumes éventuels en dépassement de rendement (sans distinction).</p>

			<p>Depuis la récolte 2016, la gestion des lies doit se faire obligatoirement par cépage. Vous inscrivez vos lies connues dans la ligne "Volume à détruire" et le système vous calculera automatiquement le volume revendiqué par cépage.</p>
			<br />
			<a href="<?php echo url_for("dr_telecharger_la_notice"); ?>" style="height: 20px;" class="telecharger-btn"></a>
		</div>
	</div>
	<?php include_partial('dr/boutons', array('display' => array('precedent','suivant'), 'dr' => $dr)) ?>
</form>
