<?php use_helper('civa') ?>
<div id="recolte_totale" class="clearfix">

    <div id="appelations">

        <table cellpadding="0" cellspacing="0" class="table_donnees">
            <thead>
                <tr>
                    <th><img src="/images/textes/appelations.png" alt="Appellations" /></th>
   <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) :?>
                    <th><?php echo preg_replace('/(AOC|Vin de table)/', '<span>\1</span>', $libelle[$a]); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Superficie (ares)</td>
                    <?php foreach ($appellations as $a)  if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td><?php echoFloat( $superficie[$a]); ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>Volume Total (Hl)</td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td><?php echoFloat( $volume[$a]); ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>Volume Revendiqué (Hl)</td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td><?php echoFloat( $revendique[$a]); ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>DPLC (Hl)</td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td><?php echoFloat( $dplc[$a]); ?></td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>

    </div>

    <div id="total_general">
        <h2 class="titre_section">Total général</h2>
        <ul class="contenu_section">
            <li><input type="text" value="<?php echoFloat( $total_superficie);?> ares" readonly="readonly"></li>
            <li><input type="text" value="<?php echoFloat( $total_volume);?> Hl" readonly="readonly"></li>
            <li><input type="text" value="<?php echoFloat( $total_revendique);?> Hl" readonly="readonly"></li>
            <li><input type="text" value="<?php echoFloat( $total_dplc);?> Hl" readonly="readonly"></li>
        </ul>
    </div>
	<div id="recap_autres">
		<table cellpadding="0" cellspacing="0" class="table_donnees autres_infos">
			<thead>
				<tr>
					<th><img src="/images/textes/autres_infos.png" alt="Appellations" /></th>
				</tr>
			</thead>
			<tbody>
				<tr>
    <td class="premiere_colonne">Jeunes Vignes : </td><td><?php echoFloat( $jeunes_vignes); ?>&nbsp;<small>ares</small></td>
				</tr>
				<tr>
    <td class="premiere_colonne">Lies :</td><td><?php echoFloat( $lies); ?>&nbsp;Hl</td>
				</tr>
					<?php if (isset($vintable['superficie'])) : ?>
				<tr>
				   <td class="premiere_colonne">Vin de table : </td><td><?php echoFloat( $vintable['superficie']); ?>&nbsp;<small>ares</small> / <?php echoFloat( $vintable['volume']); ?> Hl</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>