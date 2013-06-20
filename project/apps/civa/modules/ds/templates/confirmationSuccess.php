<?php include_partial('dsRailEtapes',array('tiers' => $tiers, 'ds' => $ds_principale, 'etape' => 6, 'force_no_link' => true, 'force_passe' => true));  ?>

<!-- #principal -->
    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#">Déclaration envoyée</a></li>
    </ul>

    <!-- #application_dr -->
    <div id="application_ds" class="clearfix">
        <div id="confirmation_fin_declaration">
            <h2 class="titre_section">Confirmation</h2>
            <div class="contenu_section">
                <div class="bloc_vert">
                    <p class="important">Votre déclaration de stocks a bien été envoyée au CIVA.</p>
                    <p>Vous pouvez retrouver tous les éléments renseignés dans votre espace CIVA.</p>
                </div>
                <div id="div-btn-email"><a href="" id="btn-email"></a></div>
            </div>
        </div>
    </div>
    <!-- fin #application_dr -->

    <ul id="btn_etape" class="btn_prev_suiv clearfix">
    <li class="prec">
        <a href="<?php echo url_for('mon_espace_civa'); ?>">
            <img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à l'étape précédente" />
        </a>
    </li>
    <li class="previsualiser">
            <a href="<?php echo url_for('ds_export_pdf', $ds_principale);?>">
        <input type="image" src="/images/boutons/btn_previsualiser.png" alt="Prévisualiser" name="boutons[previsualiser]" id="previsualiser">
            </a>
        <a href="" class="msg_aide" rel="telecharger_pdf" title="Message aide"></a>
    </li>
    </ul>
<!-- fin #principal -->

<?php //include_partial('ds/envoiMailDR') ?>

<?php include_partial('ds/generationDuPdf', array('ds' => $ds_principale)); ?>