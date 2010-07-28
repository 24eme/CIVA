<?php include_partial('global/actions') ?>

<!-- #principal -->
<form id="principal" action="<?php echo url_for('@mon_espace_civa') ?>" method="post">

    <h2 class="titre_principal">Mon espace CIVA</h2>

    <!-- #application_dr -->
    <div id="application_dr" class="clearfix">

        <!-- #nouvelle_declaration -->
        <div id="nouvelle_declaration">
            <h3 class="titre_section">Ma déclaration</h3>
            <div class="contenu_section">
                <p class="intro">Vous souhaitez faire une nouvelle déclaration :</p>
                <?php if ($declaration): ?>
                        <div class="ligne_form">
                            <input type="radio" id="type_declaration_brouillon" name="dr[type_declaration]" value="reprendre_brouillon" checked="checked" />
                            <label for="type_declaration_brouillon">A partir du brouillon</label>
                        </div>
                        <div class="ligne_form">
                            <input type="radio" id="type_declaration_suppr" name="dr[type_declaration]" value="supprimer_brouillon" />
                            <label for="type_declaration_suppr">Supprimer le brouillon</label>
                        </div>
                        <div class="ligne_form ligne_btn">
                            <input type="image" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
                        </div>
                <?php else: ?>
                        <div class="ligne_form">
                            <input type="radio" id="type_declaration_vierge" name="dr[type_declaration]" value="nouvelle" checked="checked" />
                            <label for="type_declaration_vierge">A partir d'une déclaration vierge</label>
                        </div>
                <?php if (count($campagnes) > 0): ?>
                            <div class="ligne_form">
                                <input type="radio" id="type_declaration_2" name="dr[type_declaration]" value="reprendre_ancienne" />
                                <label for="type_declaration_2">A partir d'une précédente déclaration</label>
                            </div>
                            <div class="ligne_form ligne_btn">
                                <select id="liste_precedentes_declarations" name="dr[liste_precedentes_declarations]">
                        <?php foreach ($campagnes as $id => $campagne): ?>
                                <option value="<?php echo $campagne ?>">DR <?php echo $campagne ?></option>
                        <?php endforeach; ?>
                            </select>

                            <input type="image" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
                            </div>
                <?php endif; ?>
                <?php endif; ?>
                            </div>
                        </div>
                        <!-- fin #nouvelle_declaration -->

                        <!-- #precedentes_declarations -->
                        <div id="precedentes_declarations">
                            <h3 class="titre_section">Visualiser mes déclarations</h3>
                            <div class="contenu_section">

                                <ul class="bloc_vert ui-accordion">
                                    <li>
                                        <a href="#">2010 Brouillon</a>
                        <?php if ($declaration): ?>
                                    <ul class="declarations">
                                        <li><a href="#">Brouillon courrant</a></li>
                                    </ul>
                        <?php endif; ?>
                                </li>
                                <li>
                                    <a href="#">Années précédentes</a>
                        <?php if (count($campagnes) > 0): ?>
                                        <ul class="ui-accordion">
                            <?php foreach ($campagnes as $id => $campagne): ?>
                                            <li>
                                                <a href="#"><?php echo $campagne ?></a>
                                                <ul class="declarations">
                                                    <li><a href="#<?php echo $id ?>">DR <?php echo $campagne ?></a></li>
                                                </ul>
                                            </li>
                            <?php endforeach; ?>
                                        </ul>
                        <?php endif; ?>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                            <!-- fin #precedentes_declarations -->
                        </div>
                        <!-- fin #application_dr -->

    <?php include_partial('global/boutons', array('display' => array('suivant'))) ?>
</form>
<!-- fin #principal -->