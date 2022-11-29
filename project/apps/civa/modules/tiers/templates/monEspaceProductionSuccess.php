<?php include_partial('tiers/onglets', array('active' => 'production', 'compte' => $compte, 'blocs' => $blocs)) ?>

<div id="application_dr" class="mon_espace_civa clearfix">
    <?php include_partial('tiers/title') ?>

     <div id="espace_acheteurs" class="contenu clearfix">
 	    <?php if($sf_user->hasFlash('confirmation')) : ?>
        	<p class="flash_message"><?php echo $sf_user->getFlash('confirmation'); ?></p>
		<?php endif; ?>
        <div id="nouvelle_declaration">
          <h3 class="titre_section">Déclaration de l'année<a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide"></a></h3>
          <div class="contenu_section">
              <p class="intro">Vous souhaitez :</p>
              <form action="<?= url_for($formaction, ['identifiant' => $etablissement->identifiant]) ?>" method="POST" enctype="multipart/form-data" id="form_sv">
              <?php echo $form->renderHiddenFields() ?>
              <?php echo $form->renderGlobalErrors() ?>
              <?php if ($sv): ?>
                  <div class="ligne_form">
                    <?php echo $form['action']->renderError() ?>
                    <?php echo $form['action']->render() ?>
                  </div>
              <?php else: ?>
              <div class="ligne_form">
                <ul class="radio_list">
                    <li>
                        <input name="sv_creation[type_creation]" type="radio" value="DR" id="sv_creation_type_creation_DR" checked>
                        <label for="sv_creation_type_creation_DR">Démarrer depuis les données de la DR</label>
                    </li>
                    <li>
                        <input name="sv_creation[type_creation]" type="radio" value="CSV" id="sv_creation_type_creation_CSV">
                        <label for="sv_creation_type_creation_CSV">Démarrer à partir d'un fichier</label>
                    </li>
                </ul>

                <?php echo $form['file']->renderError() ?>
                <?php echo $form['file']->renderLabel() ?>
                <?php echo $form['file']->render() ?>

                <ul class="radio_list">
                    <li>
                        <input name="sv_creation[type_creation]" type="radio" value="VIERGE" id="sv_creation_type_creation_VIERGE">
                        <label for="sv_creation_type_creation_VIERGE">Démarrer avec un document vierge</label>
                    </li>
                </ul>
              </div>
              <?php endif; ?>
              <div class="ligne_form ligne_btn">
                  <button type="submit" id="mon_espace_civa_valider" class="btn">
                      <img src="/images/boutons/btn_valider.png" alt="Valider" />
                  </button>
              </div>
              </form>
          </div>
        </div>
    </div>
    <?php if (!$sf_user->isInDelegateMode() && $sf_user->hasCredential(myUser::CREDENTIAL_DELEGATION)): ?>
        <div class="contenu clearfix">
            <?php include_component('tiers', 'delegationForm', array('form' => isset($formDelegation) ? $formDelegation : null)) ?>
        </div>
    <?php endif;?>
</div>

<div style="display: none" id="popup_loader_creation_sv" title="Génération de la SV">
    <div class="popup-loading">
    <p>La génération de votre SV est en cours.<br />Merci de patienter.<br /><small>La procédure peut prendre 30 secondes</small></p>
    </div>
</div>

<script>
var initLoadingCreationSV = function ()
{
    var btn = document.querySelector('#form_sv #mon_espace_civa_valider')
    var formSV = document.querySelector('#form_sv')
    var radioDR = document.querySelector('#form_sv #sv_creation_type_creation_DR')
    var radioCSV = document.querySelector('#form_sv #sv_creation_type_creation_CSV')
    var inputSVCsv = document.querySelector('#sv_creation_file')

    if (btn) {
      btn.addEventListener('click', function () {
        if (radioCSV.checked || radioDR.checked) {
          openPopup($("#popup_loader_creation_sv"));
        }
      })
    }

    if (inputSVCsv) {
        inputSVCsv.addEventListener('change', function () {
            radioCSV.checked = true
        })
    }

    if (formSV) {
        formSV.addEventListener('change', function () {
            if (radioCSV.checked) {
                inputSVCsv.required = true
            } else {
                inputSVCsv.removeAttribute('required')
            }
        })
    }
}

initLoadingCreationSV();
</script>
