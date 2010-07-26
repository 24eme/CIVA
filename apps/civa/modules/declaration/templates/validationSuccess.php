<?php include_partial('global/etapes', array('etape' => 3)) ?>
<?php include_partial('global/actions') ?>

<!-- #principal -->
<form id="principal" action="" method="post" class="ui-tabs">

        <ul id="onglets_majeurs" class="clearfix">
                <li><a href="#acheteurs_caves">Acheteurs et Caves</a></li>
                <li><a href="#recolte_totale">Récolte totale</a></li>
        </ul>

        <!-- #application_dr -->
        <div id="application_dr" class="clearfix">

                <!-- #acheteurs_caves -->
                <div id="acheteurs_caves">

                        <div id="acheteurs_raisin">
                                <table cellpadding="0" cellspacing="0" class="table_donnees">
                                        <thead>
                                                <tr>
                                                        <th><img src="../images/textes/acheteurs_raisin.png" alt="Acheteurs de raisin" /></th>
                                                        <th class="cvi">n°CVI</th>
                                                        <th>Raison sociale</th>
                                                        <th>Surface</th>
                                                        <th>Volume total</th>
                                                        <th>Volume revendiqué</th>
                                                        <th>DPLC</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                        <td class="nom">Nom de l'acheteur 1</td>
                                                        <td class="cvi">1454567895</td>
                                                        <td class="rs">SARL Vini</td>
                                                        <td>25</td>
                                                        <td>89</td>
                                                        <td>512</td>
                                                        <td>98</td>
                                                </tr>
                                                <tr>
                                                        <td class="nom">Nom de l'acheteur 2</td>
                                                        <td class="cvi">1454567895</td>
                                                        <td class="rs">SARL Vini</td>
                                                        <td>25</td>
                                                        <td>89</td>
                                                        <td>512</td>
                                                        <td>98</td>
                                                </tr>
                                                <tr>
                                                        <td class="nom">Nom de l'acheteur 3</td>
                                                        <td class="cvi">1454567895</td>
                                                        <td class="rs">SARL Vini</td>
                                                        <td>25</td>
                                                        <td>89</td>
                                                        <td>512</td>
                                                        <td>98</td>
                                                </tr>
                                                <tr>
                                                        <td class="nom">Nom de l'acheteur 4</td>
                                                        <td class="cvi">1454567895</td>
                                                        <td class="rs">SARL Vini</td>
                                                        <td>25</td>
                                                        <td>89</td>
                                                        <td>512</td>
                                                        <td>98</td>
                                                </tr>
                                        </tbody>
                                </table>
                        </div>

                        <div id="caves_cooperatives">
                                <table cellpadding="0" cellspacing="0" class="table_donnees">
                                        <thead>
                                                <tr>
                                                        <th><img src="../images/textes/caves_cooperatives.png" alt="Caves coopératives" /></th>
                                                        <th class="cvi">n°CVI</th>
                                                        <th>Raison sociale</th>
                                                        <th>Surface</th>
                                                        <th>Volume total</th>
                                                        <th>Volume revendiqué</th>
                                                        <th>DPLC</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                        <td class="nom">Cave 1</td>
                                                        <td class="cvi">1454567895</td>
                                                        <td class="rs">SARL Vini</td>
                                                        <td>25</td>
                                                        <td>89</td>
                                                        <td>512</td>
                                                        <td>98</td>
                                                </tr>
                                                <tr>
                                                        <td class="nom">Cave 2</td>
                                                        <td class="cvi">1454567895</td>
                                                        <td class="rs">SARL Vini</td>
                                                        <td>25</td>
                                                        <td>89</td>
                                                        <td>512</td>
                                                        <td>98</td>
                                                </tr>
                                                <tr>
                                                        <td class="nom">Cave 3</td>
                                                        <td class="cvi">1454567895</td>
                                                        <td class="rs">SARL Vini</td>
                                                        <td>25</td>
                                                        <td>89</td>
                                                        <td>512</td>
                                                        <td>98</td>
                                                </tr>
                                                <tr>
                                                        <td class="nom">Cave 4</td>
                                                        <td class="cvi">1454567895</td>
                                                        <td class="rs">SARL Vini</td>
                                                        <td>25</td>
                                                        <td>89</td>
                                                        <td>512</td>
                                                        <td>98</td>
                                                </tr>
                                        </tbody>
                                </table>
                        </div>

                </div>
                <!-- fin #acheteurs_caves -->

                <!-- #acheteurs_caves -->
                <div id="recolte_totale">

                        <div id="appelations">

                                <table cellpadding="0" cellspacing="0" class="table_donnees">
                                        <thead>
                                                <tr>
                                                        <th><img src="../images/textes/appelations.png" alt="Appelations" /></th>
                                                        <th>AOC <span>Alsace blanc</span></th>
                                                        <th><span>Klevener de<br />Heiligenstein</span></th>
                                                        <th>AOC <span>Alsace Pinot noir</span></th>
                                                        <th>AOC <span>Alsace Pinot noir rouge</span></th>
                                                        <th>AOC <span>Alsace Grand cru</span></th>
                                                        <th>AOC <span>Alsace Crément d'Alsace</span></th>
                                                        <th>Vin de table</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                        <td>Superficie (Ha)</td>
                                                        <td>362</td>
                                                        <td>362</td>
                                                        <td>58</td>
                                                        <td>5</td>
                                                        <td>19</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                </tr>
                                                <tr>
                                                        <td>Volume Total (Hl)</td>
                                                        <td>362</td>
                                                        <td>362</td>
                                                        <td>58</td>
                                                        <td>5</td>
                                                        <td>19</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                </tr>
                                                <tr>
                                                        <td>Volume Revendiqué (Hl)</td>
                                                        <td>362</td>
                                                        <td>362</td>
                                                        <td>58</td>
                                                        <td>5</td>
                                                        <td>19</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                </tr>
                                                <tr>
                                                        <td>DPLC (Hl)</td>
                                                        <td>362</td>
                                                        <td>362</td>
                                                        <td>58</td>
                                                        <td>5</td>
                                                        <td>19</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                </tr>
                                                <tr>
                                                        <td>Lies (Hl)</td>
                                                        <td>362</td>
                                                        <td>362</td>
                                                        <td>58</td>
                                                        <td>5</td>
                                                        <td>19</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                </tr>
                                        </tbody>
                                </table>

                        </div>

                        <div id="total_general">
                                <h2 class="titre_section">Total général</h2>
                                <ul class="contenu_section">
                                        <li>362Ha</li>
                                        <li>362Hl</li>
                                        <li>362Hl</li>
                                        <li>362Hl</li>
                                        <li>362Hl</li>
                                </ul>
                        </div>
                </div>
                <!-- fin #acheteurs_caves -->

        </div>
        <!-- fin #application_dr -->

       <?php include_partial('global/boutons', array('display' => array('precedent','previsualiser','suivant'))) ?>

</form>
<!-- fin #principal -->