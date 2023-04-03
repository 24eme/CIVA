<div id="popup_confirme_validationVrac" class="popup_ajout popup_confirme" title="<?php if($vrac->isVendeurProprietaire()): ?>Envoi du projet à l'acheteur<?php else: ?>Validation et envoi du projet au vendeur<?php endif; ?>">
    <form method="post" action="">
        <?php if($vrac->isVendeurProprietaire()): ?>
            <p>
                Confirmez-vous l'envoi du projet à l'acheteur ? <br />
            </p>
        <?php else: ?>
            <p>
                Avant de pouvoir signer le contrat il devra être signé par le vendeur.<br /><br />
                Confirmez-vous la validation et l'envoi du projet au vendeur pour signature ? <br />
            </p>
        <?php endif; ?>
        <div id="btns" class="clearfix" style="text-align: center; margin-top: 30px;">
            <input type="image" src="/images/boutons/btn_valider.png" alt="Valider votre contrat" name="boutons[next]" id="valideVrac_OK" class="valideDS_OK" />
            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
        </div>
    </form>
</div>
