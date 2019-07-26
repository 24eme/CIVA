<?php
use_helper('Float');
$appellations_agregee = $ds_client->getTotauxByAppellationsRecap($ds_principale);
$has_points = false;
if (isset($validation_dss)) {
    foreach ($validation_dss as $id_ds => $validation_ds) {
        if ($validation_ds->hasPoints()) {
            $has_points = true;
            break;
        }
    }
}
?>
<!-- #application_ds -->
<div id="application_ds" class="clearfix">
    <?php if (isset($validation_dss)) : ?>
        <?php if ($has_points): ?>
            <div id="validation_points_container">
                <?php foreach ($validation_dss as $id_ds => $validation_ds): ?>
                    <?php if ($validation_ds->hasPoints()): ?>
                        <h2 class="lieu_stockage"><?php echo getTitleLieuStockageStock($ds_client->find($id_ds)); ?></h2>
                    <?php endif; ?>
                    <?php include_partial('global/validation', array('validation' => $validation_ds)); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div id="recap_total_ds" class="page_recap">
        <p class="intro_declaration">Récapitulatif DRM <small>(tous lieux de stockage confondus)</small>
            <a href="" class="msg_aide_ds" rel="help_popup_ds_validation" title="Message aide"></a>
            <?php if ($ds_principale->type_ds != DSCivaClient::TYPE_DS_NEGOCE && $ds_principale->isDateDepotMairie()) : ?>
                <span style="float: right;">Date de dépot en mairie : <?php echo $ds_principale->getDateDepotMairieFr(); ?> </span>
            <?php endif; ?>
        </p>
        <div id="recap_appellations">
            <table class="table_donnees pyjama_auto">
                <thead>
                    <tr>
                        <th class="appellation">Appellations</th>
                        <th class="total">Total <span class="unites">(hl)</span></th>
                        <th>Hors VT/SGN <span class="unites">(hl)</span></th>
                        <th>VT <span class="unites">(hl)</span></th>
                        <th>SGN <span class="unites">(hl)</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appellations_agregee as $appellations_agregee_key => $appellations_agregee) : ?>
                        <tr>
                            <td class="appellation"><?php echo $appellations_agregee->nom; ?></td>
                            <?php if (!is_null($appellations_agregee->volume_total)): ?>
                                <td><?php echoFloat($appellations_agregee->volume_total); ?></td>
                                <td><?php echoFloat($appellations_agregee->volume_normal); ?></td>
                                <td><?php echoFloat($appellations_agregee->volume_vt); ?></td>
                                <td><?php echoFloat($appellations_agregee->volume_sgn); ?></td>
                            <?php else: ?>
                                <td colspan="4" class="neant neant_alt">Néant</td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div id="total" class="ligne_total">
                <h3>Total AOC</h3>
                <input type="text" readonly="readonly" value="<?php echoFloat($ds_client->getTotalAOC($ds_principale)); ?>" />
            </div>				                           
        </div>
        
        <div id="recap_autres_vins_sans_ig">
            
        <div id="recap_autres">				
            <table class="table_donnees pyjama_auto">
                <thead>
                    <tr>
                        <th class="appellation">Autres</th>
                        <th class="total">Total <span class="unites">(hl)</span></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($ds_client->getRecapAutres($ds_principale) as $nom => $valeur)  : ?>
                    <tr>
                        <td class="appellation"><?php echo $nom ; ?></td>
                        <td><?php echoFloat($valeur); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php $recapVCI = $ds_client->getTotauxVCIRecap($ds_principale); ?>
        <?php if(count($recapVCI)): ?>
        <div id="recap_vins_sans_ig">
            <table class="table_donnees pyjama_auto">
                <thead>
                    <tr>
                        <th class="appellation">VCI&nbsp;<a title="Message aide" rel="help_popup_validation_vci" class="msg_aide_ds" href=""></a></th>
                        <th class="total">Total <span class="unites">(hl)</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recapVCI as $libelle => $volume): ?>
                        <tr>
                            <td class="appellation"><?php echo $libelle ?></td>
                            <td><?php echoFloat($volume); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div id="recap_vins_sans_ig">
            <table class="table_donnees pyjama_auto">
                <thead>
                    <tr>
                        <th class="appellation">Vins sans IG&nbsp;<a title="Message aide" rel="help_popup_validation_vins_sans_ig" class="msg_aide_ds" href=""></a></th>
                        <th class="total">Total <span class="unites">(hl)</span></th>
                    </tr>
                </thead>
                <tbody>        
                    <tr>
                        <td class="appellation">Vins Sans IG</td>
                        <td><?php echoFloat($ds_client->getTotalSansIG($ds_principale)); ?></td>
                    </tr>        
                    <tr>
                        <td class="appellation">Mousseux</td>
                        <td><?php echoFloat($ds_client->getTotalSansIGMousseux($ds_principale)); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
            
        </div>
        <?php if ($isAdmin): ?>
        <div id="administration_validation">
            <h2 class="titre_section">Administration de la validation</h2>
            <div class="contenu_section">
                <div class="bloc_gris presentation">
                <div class="bloc_form">
                        <?php echo $formDatesModification->renderGlobalErrors(); ?>
                        <div class="ligne_form ">
                            <?php echo $formDatesModification['date_edition']->renderLabel(); ?>
                            <?php echo $formDatesModification['date_edition']->renderError(); ?>
                            <?php echo $formDatesModification['date_edition']->render(array('class' => "datepicker")); ?>
                        </div>
                        <div class="ligne_form ">     
                            <?php echo $formDatesModification['date_validation']->renderLabel(); ?>
                            <?php echo $formDatesModification['date_validation']->renderError(); ?>
                            <?php echo $formDatesModification['date_validation']->render(array('class' => "datepicker")); ?>
                        </div>
                        <div class="ligne_form ">        
                            <?php echo $formDatesModification['utilisateurs']->renderLabel(); ?>
                            <?php echo $formDatesModification['utilisateurs']->renderError(); ?>
                            <?php echo $formDatesModification['utilisateurs']->render(); ?>
                        </div>
                </div>
                </div>
            </div>
        </div>
            <?php endif; ?>

    </div>
    </div>
    <!-- fin #application_ds -->
