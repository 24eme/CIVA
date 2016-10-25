<!-- #principal -->
    <!-- #application_ds -->
    <div id="application_ds" class="clearfix">
        <div id="feed_back">
            <h2 class="titre_section">Votre avis</h2>
            <div class="contenu_section">
                <div class="bloc_vert">
                    <p class="important">Votre avis a bien été envoyé au CIVA</p>
                    <p>Merci d'avoir pris le temps de nous avoir fait part de votre commentaire.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- fin #application_ds -->

    <ul id="btn_etape" class="btn_prev_suiv clearfix">
    <li class="prec">
        <a href="<?php echo url_for('mon_espace_civa_dr', $dr->getEtablissement()); ?>">
            <img src="/images/boutons/btn_retourner_mon_espace.png" alt="Retourner à l'étape précédente" />
        </a>
    </li>
    </ul>
<!-- fin #principal -->
