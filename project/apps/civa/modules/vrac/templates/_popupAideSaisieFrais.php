<div id="popup_vendeur_frais_annexes" class="popup_ajout" title="Frais annexes en sus à la charge du vendeur">
    <table>
        <thead>
            <tr>
                <th>Type de frais</th>
                <th>Montant</th>
                <th>Unité</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select style="width:120px;margin:0px;" name="type">
                        <option value="de frais de transport">Transport</option>
                        <option value="de frais de courtage">Courtage</option>
                        <option value="en autres frais">Autres</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="num" name="montant" style="width:100px; text-align: right; padding-right: 5px;" />
                </td>
                <td>
                    <select style="width:100px;margin:0px;" name="unite">
                        <option value="€ HT/hl">€ HT/hl</option>
                        <option value="€ HT/kg">€ HT/kg</option>
                        <option value="€ HT">€ HT</option>
                        <option value="%">%</option>
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
            $(".aideSaisieFraisPopup").trigger("click");
        }
    });
    $("#popup_vendeur_frais_annexes").keypress(function(e) {if(e.which == 13) $("#popup_vendeur_frais_annexes .ajouter").trigger("click");});
    $("#popup_vendeur_frais_annexes .ajouter").click(function() {
        var type = $('#popup_vendeur_frais_annexes select[name="type"]').val();
        var montant = $('#popup_vendeur_frais_annexes input[name="montant"]').val();
        var unite = $('#popup_vendeur_frais_annexes select[name="unite"]').val();
        var ligne = montant+unite+' '+type;
        var contenu = $('#<?php echo $target ?>').val();
        if (contenu) {
            contenu += "\n";
        }
        if (montant) {
            $('#<?php echo $target ?>').val(contenu+ligne);
        }
        $("#popup_vendeur_frais_annexes a.close_popup").trigger("click");
        $('#popup_vendeur_frais_annexes select[name="type"]').val($('#popup_vendeur_frais_annexes select[name="type"] option:first').val());
        $('#popup_vendeur_frais_annexes input[name="montant"]').val('');
        $('#popup_vendeur_frais_annexes select[name="unite"]').val($('#popup_vendeur_frais_annexes select[name="unite"] option:first').val());
        return false;
    });
</script>
