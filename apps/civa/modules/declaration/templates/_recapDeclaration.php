<div id="recolte_totale" class="clearfix">

    <div id="appelations">

        <table cellpadding="0" cellspacing="0" class="table_donnees">
            <thead>
                <tr>
                    <th><img src="/images/textes/appelations.png" alt="Appelations" /></th>
                    <?php foreach ($appellations as $a) : ?>
                    <th><?php echo preg_replace('/(AOC|Vin de table)/', '<span>\1</span>', $libelle[$a]); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Superficie (ares)</td>
                    <?php foreach ($appellations as $a) : ?>
                    <td><?php echo $superficie[$a]; ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td>Volume Total (Hl)</td>
                    <?php foreach ($appellations as $a) : ?>
                    <td><?php echo $volume[$a]; ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td>Volume Revendiqué (Hl)</td>
                    <?php foreach ($appellations as $a) : ?>
                    <td><?php echo $revendique[$a]; ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td>DPLC (Hl)</td>
                    <?php foreach ($appellations as $a) : ?>
                    <td><?php echo $dplc[$a]; ?></td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>

    </div>

    <div id="total_general">
        <h2 class="titre_section">Total général</h2>
        <ul class="contenu_section">
            <li><input type="text" value="<?php echo $total_superficie;?> ares" readonly="readonly"></li>
            <li><input type="text" value="<?php echo $total_volume;?> Hl" readonly="readonly"></li>
            <li><input type="text" value="<?php echo $total_revendique;?> Hl" readonly="readonly"></li>
            <li><input type="text" value="<?php echo $total_dplc;?> Hl" readonly="readonly"></li>
        </ul>
    </div>
	
	<div id="recap_autres">
		<table cellpadding="0" cellspacing="0" class="table_donnees autres_infos">
			<thead>
				<tr>
					<th><img src="/images/textes/autres_infos.png" alt="Appelations" /></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="premiere_colonne">Jeunes Vignes : </td><td><?php echo $jeunes_vignes; ?>&nbsp;<small>ares</small></td>
				</tr>
				<tr>
					<td class="premiere_colonne">Lies :</td><td><?php echo $lies; ?>Hl</td>
				</tr>
				<?php if (isset($superficie['VINTABLE'])) : ?>
				<tr>
					<td class="premiere_colonne">Vin de table : </td><td><?php echo $superficie['VINTABLE']; ?>&nbsp;<small>ares</small> / <?php echo $volume['VINTABLE']; ?> Hl</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>