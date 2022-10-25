<ol class="breadcrumb">
    <li><a href="<?php echo url_for('sv') ?>">SV11 / SV12</a></li>
    <li class="active"><a href="<?php echo url_for('sv_etablissement', array('identifiant' => $etablissement->identifiant)) ?>"><?php echo $etablissement->nom ?> (<?php echo $etablissement->identifiant ?>)</a></li>
</ol>

<div id="nouvelle_declaration">
  <h3 class="titre_section">Déclaration de l'année<a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide"></a></h3>
  <div class="contenu_section">
      <p class="intro">Vous souhaitez :</p>
      <form action="<?= url_for('sv_etablissement', ['identifiant' => $etablissement->identifiant]) ?>" method="POST">
        <div class="ligne_form">
          <?php echo $formCreation['type_creation']->renderError() ?>
          <?php echo $formCreation['type_creation']->render() ?>
        </div>
        <div class="ligne_form ligne_btn">
           <button type="submit" id="mon_espace_civa_valider" class="btn">
              <img src="/images/boutons/btn_valider.png" alt="Valider" />
           </button>
        </div>
      </form>
  </div>
</div>

<div style="clear: both;"></div>
