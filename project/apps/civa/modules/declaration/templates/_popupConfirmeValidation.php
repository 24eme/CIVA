<div id="popup_confirme_validation" class="popup_ajout popup_confirme" title="Validation de votre DR">
    <form method="post" action="">
        <p>
            Une fois votre déclaration validée, vous ne pourrez plus la modifier. <br /><br />
            Confirmez vous la validation de votre déclaration de récolte ?<br />
        </p>
        <div style="margin-top: 15px;" class="ligne_form">
        <label>Autorisations de transmission de votre Déclaration de Récolte :</label>
        </div>
        <div class="ligne_form">
                <input id="checkbox_partage_acheteurs" checked="checked" style="float:left; margin-right: 8px; margin-left: 0px; margin-top: 3px;" type="checkbox" value="" />
                <label for="checkbox_partage_acheteurs">À vos acheteurs</label>
        </div>
        <div class="ligne_form">
            <input id="checkbox_partage_ava" checked="checked" style="float:left; margin-right: 8px; margin-left: 0px; margin-top: 3px;" type="checkbox" value="" />
            <label for="checkbox_partage_ava">À l'AVA pour télédéclarer votre Déclaration de Revendication</label>
        </div>
        <div id="btns">
            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
            <input type="image" src="/images/boutons/btn_valider.png" alt="Valider votre déclaration" name="boutons[next]" id="valideDR" class="valideDR_OK" />
        </div>
    </form>
</div>
