<form id="principal" action="" method="post">
	<h2 class="titre_principal">Important</h2>
	<div id="notice_evolutions">
        <h2>Rendements 2026</h2>
		<p style="font-size: 14px; margin-top: 15px;">Depuis la Récolte 2020 les rendements de l'AOC Alsace blanc sont calculés <strong>PAR CÉPAGE</strong>. Par conséquent pour déclarer de l'Assemblage (Edel), il est OBLIGATOIRE de déclarer une surface prise en déduction des surfaces des autres cépages blancs. Pour plus de détails vous pouvez consulter la 'Notice d'aide'.</p>
		<br />
        <h2>Constitution de VCI pour la Récolte 2026</h2>
		<p style="font-size: 14px; margin-top: 15px;">Appellations :<br />
			<ul style="font-size: 14px; padding-left: 20px; list-style-type: initial;">
			    <li style="padding-top: 5px;">
					AOC Alsace : 10 hl/ha <strong>PAR CÉPAGE</strong>
			    </li>
			    <li style="padding-top: 5px;">
					AOC Crémant d’Alsace : 10 hl/ha pour l'Appellation, à constituer dans les différents cépages
			    </li>
			</ul>
		</p>
		<p style="font-size: 14px; margin-top: 10px;">Pour plus de précisions lire le <u><a href="<?php echo url_for('dr_telecharger_guide_vci') ?>">guide du VCI</a></u> dans la rubrique 'Documents d'Aide'</p>
		<br />
        <h2>Rebêches</h2>
        <br />
        <p>Suite à la modification temporaire du cahier des charges de l’AOP « Crémant d’Alsace », le volume correspondant au pourcentage initialement isolé en tant que « rebêches » pourra être requalifié en moût pouvant prétendre à l’AOP si toutes les conditions suivantes sont réunies :</p>
        <ul>
            <li>il est encore physiquement disponible, séparé, identifié et intégralement traçable ;</li>
            <li>il n’a pas fait l’objet d’une sortie, d’une cession, d’une destruction, d’une dénaturation ou d’une affectation irréversible à une autre destination</li>
            <li>La réintégration des rebêches ne doit pas conduire au dépassement
                <ul>
                    <li>du rendement fixé à 80 hectolitres par hectare,</li>
                    <li>de la limite d’obtention de 100 litres de moûts pour 150 kilogrammes de raisins mis en oeuvre.</li>
                </ul>
            </li>
        </ul>
        <p>Les rebêches doivent désormais être déclarées par couleur et être comprises entre 0% et 10%</p>

		<h2>Rappels</h2>
		<p style="font-size: 14px; margin-top: 15px;">La production de Jus de Raisin doit obligatoirement figurer sur la Déclaration de Récolte Alsace dans la rubrique 'Récolte Autres'. Les surfaces déclarées en production de jus de raisin viendront en déduction de la surface déclarée en AOC. Il n'existe pas de rendement maximum pour ce produit, la préconisation est de respecter un rendement agronomique réaliste.</p>
		<br />

		<div style="font-size: 14px; padding-left: 5px;">
			<p style="font-size: 14px; margin-top: 15px;">Depuis la récolte 2016, un rendement spécifique est mis en place pour les mentions VT et SGN (le calcul du rendement se fait par Appellation/Cépage/Mention) :<br />
				<ul style="font-size: 14px; padding-left: 20px; list-style-type: initial;">
				    <li style="padding-top: 5px;">
						Vendanges Tardives : 55 hl/ha <strong>(Attention 50 hl/ha pour l'AOC Grand Cru, sauf pour le GW Lieu-dit 47 hl/ha)</strong>
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
