<?php use_helper('Float') ?>

<div id="recolte_totale" class="clearfix">

    <div id="appelations">

        <table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto">
            <thead>
                <tr>
                    <th><img src="/images/textes/appelations.png" alt="Appellations" /></th>
   <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) :?>
                    <th id="recap_th_<?php echo $a ?>"><?php echo preg_replace('/(AOC|Vin de table|Mention)/', '<span>\1</span>', $libelle[$a]); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Superficie (ares)</td>
                    <?php foreach ($appellations as $a)  if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume"><?php echoFloat($superficie[$a]); ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>Volume Total (hl)</td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume"><?php echoFloat($volume[$a]); ?></td>
                    <?php endif; ?>
                </tr>
                <?php if(isset($total_volume_vendus)): ?>
                <tr>
                    <td>Volume vendu (hl)</td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume"><?php echoFloat($volume_vendus[$a]); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Volume sur place (hl)</td>
                    <?php foreach ($appellations as $a)  if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume"><?php echoFloat($volume_sur_place[$a]); ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <td>Volume Revendiqué (hl)</td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume"><?php echoFloat($revendique[$a]); ?></td>
                    <?php endif; ?>
                </tr>
                <?php if(isset($total_revendique_sur_place)): ?>
                <tr class="small">
                    <td>&nbsp;dont sur place (hl)</td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume"><?php echoFloat($revendique_sur_place[$a]); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <?php if($has_no_usages_industriels): ?>
                            DPLC (hl)
                        <?php else: ?>
                            Volume à détruire (hl)
                        <?php endif; ?>
                    </td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume">
                        <?php if($has_no_usages_industriels): ?>
                            <?php echoFloat($usages_industriels[$a]); ?>
                        <?php elseif($usages_industriels[$a] != 0): ?>
                            <?php echoFloat($usages_industriels[$a]); ?>
                        <?php endif; ?>
                    </td>
                    <?php endif;  ?>
                </tr>
                <?php if(isset($total_usages_industriels_sur_place)): ?>
                <tr class="small">
                    <td>&nbsp;dont sur place (hl)</td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume">
                    <?php if($usages_industriels[$a] != 0): ?>
                        <?php echoFloat($usages_industriels_sur_place[$a]); ?>
                    <?php endif; ?>
                    </td>
                    <?php endif;  ?>
                </tr>
                <?php endif; ?>
                <?php if(isset($total_volume_rebeches)): ?>
                <tr>
                    <td>Rebêches (hl)</td>
                    <?php foreach ($appellations as $a)  if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume">
                    	<?php if(isset($volume_rebeches[$a])): ?>
                        	<?php echoFloat($volume_rebeches[$a]); ?>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <tr class="small">
                    <td>&nbsp;dont sur place (hl)</td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume">
                    	<?php if(isset($volume_rebeches_sur_place[$a])): ?>
                        	<?php echoFloat($volume_rebeches_sur_place[$a]); ?>
                    	<?php endif; ?>
                    </td>
                    <?php endif;  ?>
                </tr>
                <?php endif; ?>
                <?php if(isset($volume_vci[$a])): ?>
                <tr>
                    <td>
                        VCI (hl)
                    </td>
                    <?php foreach ($appellations as $a) if (!isset($ignore[$a]) || !$ignore[$a]) : ?>
                    <td class="volume">
                        <?php if(isset($volume_vci[$a]) && $volume_vci[$a]): ?>
                            <?php echoFloat($volume_vci[$a]); ?>
                        <?php endif; ?>
                    </td>
                    <?php endif;  ?>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
   </div>

    <div id="total_general">
        <h2 class="titre_section">Total général</h2>
        <ul class="contenu_section">
            <li><input type="text" value="<?php echoFloat($total_superficie);?>" readonly="readonly"></li>
            <li><input type="text" value="<?php echoFloat($total_volume);?>" readonly="readonly"></li>
            <?php if(isset($total_volume_vendus)): ?>
            <li><input type="text" value="<?php echoFloat($total_volume_vendus);?>" readonly="readonly"></li>
            <?php endif; ?>
            <li><input type="text" value="<?php echoFloat($total_volume_sur_place);?>" readonly="readonly"></li>
            <li><input type="text" value="<?php echoFloat($total_revendique);?>" readonly="readonly"></li>
            <?php if(isset($total_revendique_sur_place)): ?>
            <li class="small"><input type="text" value="<?php echoFloat($total_revendique_sur_place);?>" readonly="readonly"></li>
            <?php endif; ?>
            <li>
                <?php if($has_no_usages_industriels): ?>
                    <input type="text" value="<?php echoFloat($total_usages_industriels);?>" readonly="readonly">
                <?php else: ?>
                    <input type="text" value="<?php echoFloat($total_usages_industriels);?>" readonly="readonly">
                <?php endif; ?>
            </li>
            <?php if(isset($total_usages_industriels_sur_place) && !$has_no_usages_industriels): ?>
            <li class="small"><input type="text" value="<?php echoFloat($total_usages_industriels_sur_place);?>" readonly="readonly"></li>
            <?php endif; ?>
            <?php if(isset($total_volume_rebeches)): ?>
            <li><input type="text" value="<?php echoFloat($total_volume_rebeches);?>" readonly="readonly"></li>
            <li class="small"><input type="text" value="<?php echoFloat($total_volume_rebeches_sur_place);?>" readonly="readonly"></li>
            <?php endif; ?>
            <?php if(isset($total_volume_vci)): ?>
            <li><input type="text" value="<?php echoFloat($total_volume_vci);?>" readonly="readonly"></li>
            <?php endif; ?>
        </ul>
    </div>
	<div id="recap_autres">
		<table cellpadding="0" cellspacing="0" class="table_donnees pyjama_auto autres_infos">
			<thead>
				<tr>
					<th><img src="/images/textes/autres_infos.png" alt="Appellations" /></th>
				</tr>
			</thead>
			<tbody>
                <?php if($has_no_usages_industriels): ?>
                <tr>
                    <td class="premiere_colonne">Lies : </td><td><?php echoFloat($lies); ?>&nbsp;<small>hl</small></td>
                </tr>
                <?php endif; ?>
				<tr>
                    <td class="premiere_colonne">Jeunes Vignes : </td><td class="volume"><?php echoFloat($jeunes_vignes); ?>&nbsp;<small>ares</small></td>
				</tr>
			    <?php if (isset($vintable['superficie'])) : ?>
				<tr>
				   <td class="premiere_colonne">Vins sans IG : </td><td class="volume"><?php echoFloat($vintable['superficie']); ?>&nbsp;<small>ares</small> / <?php echoFloat($vintable['volume']); ?> hl</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
