<div id="popup_conditions_paiement" class="popup_ajout" title="Délais de paiement">
    <form method="post" action="">
        <table>
            <tr>
                <th align="right">Délai :</th>
                <td align="left">
                    <select style="width:280px;margin-bottom:5px" name="delai">
                        <option value="Délai légal : 60 jours après la date d’émission de la facture">Délai légal : 60 jours après la date d’émission de la facture</option>
                        <option value="Paiement sous 7 jours">Paiement sous 7 jours</option>
                    </select>
                </td>
            </tr>
            <tr id="ligneEscompte">
                <th align="right">Escompte :</th>
                <td align="left">
                    <input type="text" name="escompte" style="width:100px;" />&nbsp;%
                </td>
            </tr>
        </table>
        <div class="clearfix" style="text-align: center; margin-top: 15px;">
            <input type="image" src="/images/boutons/btn_valider.png" alt="Ajouter" name="boutons[next]" class="ajouter" />
            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
        </div>
    </form>
</div>
<script type="text/javascript">
    $('#<?php echo $target ?>').focus(function() {
        if (!$('#<?php echo $target ?>').val()) {
            $(".aideSaisieDelaiPaiementPopup").trigger("click");
        }
    });
    $('#ligneEscompte').hide();
    $('#popup_conditions_paiement select[name="delai"]').change(function() {
        if ($(this).val() == 'Paiement sous 7 jours') {
            $('#ligneEscompte').show();
        }
    });
    $("#popup_conditions_paiement .ajouter").click(function() {
        var delai = $('#popup_conditions_paiement select[name="delai"]').val();
        var escompte = $('#popup_conditions_paiement input[name="escompte"]').val();
        var ligne = delai;
        if (escompte) {
            ligne += ', avec escompte de '+escompte+'%';
        }
        $('#<?php echo $target ?>').val(ligne);
        $("#popup_conditions_paiement a.close_popup").trigger("click");
        $('#ligneEscompte').hide();
        $('#popup_conditions_paiement select[name="delai"]').val($('#popup_conditions_paiement select[name="delai"] option:first').val());
        $('#popup_conditions_paiement input[name="escompte"]').val('');
        return false;
    });
</script>
