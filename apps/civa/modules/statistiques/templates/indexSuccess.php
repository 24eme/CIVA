 <h2 class="titre_principal">Statistiques</h2>

    <!-- #application_dr -->
    <div class="clearfix" id="application_dr">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
            <h3 class="titre_section">Statistiques des DR de l'année</h3>
            <div class="contenu_section">
                <ul class="statistiques">
                    <li><strong>Nombre d'inscrits : <?php echo $nbInscrit; ?></strong>
                    <li><strong>Nombre d'inscrits en cours de mot de passe oublié  : <?php echo $nbOublie; ?></strong>
                    <li><strong>Nombre de déclarations validées : <?php echo $etapeDrValidee; ?></strong></li>
                    <li><strong>Nombre de déclarations en cours : <?php echo $etapeDrNonValidee; ?></strong>
                        <ul>
                            <li>à l'étape exploitation : <?php echo $etapeExploitation; ?></li>
                            <li>à l'étape récolte : <?php echo $etapeRecolte; ?></li>
                            <li>à l'étape validation : <?php echo $etapeValidation; ?></li>
                        </ul>
                    </li>
			    <li><strong>Nombre de CSV acheteurs uploadés : <?php echo link_to($nb_csv_acheteurs, '@upload_list'); ?></strong></li>
                </ul>
            </div>
            <br />
            <h3 class="titre_section">Statistiques Alsace Gamm@</h3>
            <div class="contenu_section">
                <ul class="statistiques">
                    <li><strong>Nombre d'inscrits : <?php echo $nbInscritGamma; ?></strong>
                </ul>
            </div>
        </div>
    </div>