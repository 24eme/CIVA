<div id="popup_prix_non_determine" class="popup_ajout" title="Prix non determiné">
    <table>
        <thead>
            <tr style="display: flex;justify-content: space-around;">
                <th style="width:100px;">Montant</th>
                <th style="width:100px;">Unité</th>
            </tr>
        </thead>
        <tbody>
            <tr style="display: flex;justify-content: space-around;">
                <td>
                    <input type="text" class="num" name="montant" style="width:140px; text-align: right; margin:0;" />
                </td>
                <td>
                    <select style="width:140px;margin:0px;" name="unite">
                        <option value="€ HT/hl">€ HT/hl</option>
                        <option value="€ HT/kg">€ HT/kg</option>
                        <option value="€ HT">€ HT</option>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="clearfix" style="text-align: center; margin-top: 15px;">
        <input type="image" src="/images/boutons/btn_valider.png" alt="Ajouter" name="boutons[next]" class="ajouter" />
        <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
    </div>
</div>
<script type="text/javascript">
    $('#<?php echo $target ?>').focus(function() {
        if (!$('#<?php echo $target ?>').val()) {
            $(".aideSaisiePrixNDPopup").trigger("click");
        }
    });
    $("#popup_prix_non_determine").keypress(function(e) {if(e.which == 13) $("#popup_prix_non_determine .ajouter").trigger("click");});
    $("#popup_prix_non_determine .ajouter").click(function() {
        var montant = $('#popup_prix_non_determine input[name="montant"]').val();
        var unite = $('#popup_prix_non_determine select[name="unite"]').val();
        var ligne = montant+unite;
        var contenu = $('#<?php echo $target ?>').val();
        if (contenu) {
            contenu += "\n";
        }
        if (montant) {
            $('#<?php echo $target ?>').val(contenu+ligne);
        }
        $("#popup_prix_non_determine a.close_popup").trigger("click");
        $('#popup_prix_non_determine input[name="montant"]').val('');
        $('#popup_prix_non_determine select[name="unite"]').val($('popup_prix_non_determine select[name="unite"] option:first').val());
        return false;
    });
</script>
