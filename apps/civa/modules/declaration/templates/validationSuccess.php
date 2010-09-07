<?php include_partial('global/etapes', array('etape' => 3)) ?>
<?php include_partial('global/actions') ?>

<!-- #principal -->
<form id="principal" action="" method="post" class="ui-tabs">

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#recolte_totale">Récolte totale</a></li>
    </ul>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">
        <div id="validation_dr">
        <p class="intro_declaration">Veuillez vérifier les informations saisies avant de valider votre déclaration.</p>

        <?php if($error){ ?>
            <div class="message">
                <?php foreach($validLog as $logs) { ?>
                <ul class="messages_log">
                    <?php foreach($logs as $log) { ?>
                        <li><a href="<?php echo $log['url']; ?>"><?php echo $log['log']; ?></a></li>
                   <?php } ?>
                </ul>
                <br />
                <?php } ?>
            </div>
        <?php } ?>

        <!-- #acheteurs_caves -->
        <div id="recolte_totale">

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
        </div>
        <div id="extra recap" style="clear: both;">
            <table cellpadding="0" cellspacing="0" class="table_donnees autres_infos">
                <thead>
                    <tr>
                        <th><img src="/images/textes/autres_infos.png" alt="Appelations" /></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($jeunes_vignes>0){ ?>
                    <tr>
                        <td class="premiere_colonne">Jeunes Vignes : </td><td><?php echo $jeunes_vignes; ?>&nbsp;<small>ares</small></td>
                    </tr>
                    <?php }?>
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
        <!-- fin #acheteurs_caves -->
        </div>
    </div>
    <!-- fin #application_dr -->
    <?php if ($annee == '2010') : ?>
        <?php if($error){ ?>
            <?php include_partial('global/boutons', array('display' => array('precedent','previsualiser'))) ?>
        <?php }else{?>
            <?php include_partial('global/boutons', array('display' => array('precedent','previsualiser','valider'))) ?>
        <?php } ?>
    <?php endif; ?>
</form>
<!-- fin #principal -->
<script>
    ajax_url_to_print = "<?php echo url_for('@print?annee='.$annee); ?>?ajax=1";
</script>
<div id="popup_loader" title="Génération du PDF">
    <div class="ui-autocomplete-loading popup-loading"></div>
</div>