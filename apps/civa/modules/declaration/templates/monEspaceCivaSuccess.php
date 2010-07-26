<?php include_partial('global/actions') ?>

<!-- #principal -->
<form id="principal" action="" method="post">

        <h2 class="titre_principal">Mon espace CIVA</h2>

        <!-- #application_dr -->
        <div id="application_dr" class="clearfix">

                <!-- #nouvelle_declaration -->
                <div id="nouvelle_declaration">
                        <h3 class="titre_section">Nouvelle déclaration</h3>
                        <div class="contenu_section">
                                <p class="intro">Vous souhaitez faire une nouvelle déclaration :</p>
                                <div class="ligne_form">
                                        <input type="radio" id="type_declaration_1" name="dr[type_declaration]" value="type_declaration_1" checked="checked" />
                                        <label for="type_declaration_1">A partir d'une déclaration vierge</label>
                                </div>
                                <div class="ligne_form">
                                        <input type="radio" id="type_declaration_2" name="dr[type_declaration]" value="type_declaration_2" />
                                        <label for="type_declaration_2">A partir d'une précédente déclaration</label>
                                </div>

                                <div class="ligne_form ligne_btn">
                                        <select id="liste_precedentes_declarations" name="dr[liste_precedentes_declarations]">
                                                <option value="">DR 2009</option>
                                                <option value="">DR 2008</option>
                                                <option value="">DR 2007</option>
                                                <option value="">DR 2006</option>
                                                <option value="">DR 2005</option>
                                        </select>

                                        <input type="image" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
                                </div>
                        </div>
                </div>
                <!-- fin #nouvelle_declaration -->

                <!-- #precedentes_declarations -->
                <div id="precedentes_declarations">
                        <h3 class="titre_section">Mes précédentes déclarations</h3>
                        <div class="contenu_section">

                                <ul class="bloc_vert ui-accordion">
                                        <li>
                                                <a href="#">2010 Brouillon</a>
                                                <ul class="declarations">
                                                        <li><a href="#">Brouillon 1</a></li>
                                                        <li><a href="#">Brouillon 2</a></li>
                                                </ul>
                                        </li>
                                        <li>
                                                <a href="#">Années précédentes</a>
                                                <ul class="ui-accordion">
                                                        <li>
                                                                <a href="#">2009</a>
                                                                <ul class="declarations">
                                                                        <li><a href="#">DR 2009</a></li>
                                                                </ul>
                                                        </li>
                                                        <li>
                                                                <a href="#">2008</a>
                                                                <ul class="declarations">
                                                                        <li><a href="#">DR 2008</a></li>
                                                                </ul>
                                                        </li>
                                                        <li>
                                                                <a href="#">2007</a>
                                                                <ul class="declarations">
                                                                        <li><a href="#">DR 2007</a></li>
                                                                </ul>
                                                        </li>
                                                        <li>
                                                                <a href="#">2006</a>
                                                                <ul class="declarations">
                                                                        <li><a href="#">DR 2006</a></li>
                                                                </ul>
                                                        </li>
                                                        <li>
                                                                <a href="#">2005</a>
                                                                <ul class="declarations">
                                                                        <li><a href="#">DR 2005</a></li>
                                                                </ul>
                                                        </li>
                                                </ul>
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