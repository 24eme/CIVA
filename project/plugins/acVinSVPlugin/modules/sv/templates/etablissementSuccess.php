<ol class="breadcrumb">
    <li><a href="<?php echo url_for('sv') ?>">SV11 / SV12</a></li>
    <li class="active"><a href="<?php echo url_for('sv_etablissement', array('identifiant' => $etablissement->identifiant)) ?>"><?php echo $etablissement->nom ?> (<?php echo $etablissement->identifiant ?>)</a></li>
</ol>

<div id="nouvelle_declaration">
  <h3 class="titre_section">Déclaration de l'année<a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide"></a></h3>
  <div class="contenu_section">
      <p class="intro">Vous souhaitez :</p>
        <div class="ligne_form">
            <input type="radio" id="type_declaration_visualisation_avant_import" name="dr[type_declaration]" value="visualisation_avant_import" checked="checked" />
            <label for="type_declaration_visualisation_avant_import">Démarrer depuis les données de la DR</label>
        </div>
        <div class="ligne_form">
            <input type="radio" id="type_declaration_import" name="dr[type_declaration]" value="import" />
            <label for="type_declaration_import">Démarrer à partir d'un fichier</label>
        </div>
        <div class="ligne_form">
            <input type="radio" id="type_declaration_vierge" name="dr[type_declaration]" value="vierge" />
            <label for="type_declaration_vierge">Démarrer d'une déclaration vierge</label>
        </div>

      <div class="ligne_form ligne_btn">
         <a href="<?php echo url_for('sv_apporteurs', $sv) ?>" id="mon_espace_civa_valider" class="btn"><img src="/images/boutons/btn_valider.png" alt="Valider" /></a>
      </div>
  </div>
</div>

<div style="clear: both;"></div>
