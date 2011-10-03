<form id="form_dr" action="<?php echo url_for('@declaration_init') ?>" method="post">
    <h3 class="titre_section">Déclaration de l'année<a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide"></a></h3>
    <div class="contenu_section">
        <p class="intro">Vous souhaitez faire une nouvelle déclaration :</p>
        <?php if (!$declaration->isNew()): ?>
            <div class="ligne_form">
                <input type="radio" id="type_declaration_brouillon" name="dr[type_declaration]" value="brouillon" checked="checked" />
                <label for="type_declaration_brouillon">Continuer ma déclaration</label>
            </div>
            <div class="ligne_form">
                <input type="radio" id="type_declaration_suppr" name="dr[type_declaration]" value="supprimer" />
                <label for="type_declaration_suppr">Supprimer ma déclaration <?php echo $sf_user->getCampagne() ?> en cours</label>
            </div>
        <?php else: ?>
            <div class="ligne_form">
                <input type="radio" id="type_declaration_vierge" name="dr[type_declaration]" value="vierge" checked="checked" />
                <label for="type_declaration_vierge">A partir d'une déclaration vierge</label>
            </div>
   <?php if ($has_import) : ?>
            <div class="ligne_form">
                    <input type="radio" id="type_declaration_import" name="dr[type_declaration]" value="import" />
                    <label for="type_declaration_import">A partir des informations des coopératives ou acheteurs</label>
            </div>
   <?php endif; if (count($campagnes) > 0): ?>
                <div class="ligne_form">
                    <input type="radio" id="type_declaration_precedente" name="dr[type_declaration]" value="precedente" />
                    <label for="type_declaration_precedente">A partir d'une précédente déclaration</label>
                </div>
                <div class="ligne_form ligne_btn">
                    <select id="liste_precedentes_declarations" name="dr[liste_precedentes_declarations]">
                        <?php foreach ($campagnes as $id => $campagne): ?>
                            <option value="<?php echo $campagne ?>">DR <?php echo $campagne ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="ligne_form ligne_btn">
            <input type="image" name="boutons[valider]" id="mon_espace_civa_valider" class="btn" src="../images/boutons/btn_valider.png" alt="Valider" />
        </div>
        <p class="intro msg_mon_espace_civa"><?php echo sfCouchdbManager::getClient('Messages')->getMessage('intro_mon_espace_civa_dr'); ?></p>
    </div>
</form>