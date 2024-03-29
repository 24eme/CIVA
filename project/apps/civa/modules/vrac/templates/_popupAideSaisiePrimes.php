<div id="popup_acheteur_primes_diverses" class="popup_ajout" title="Primes diverses à la charge de l’acheteur">
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
                        <option value="d'apport global">Apport global</option>
                        <option value="d'engagement surface/volume">Engagement surface / volume</option>
                        <option value="de vendange manuelle">Vendange Manuelle</option>
                        <option value="de bonification/Réfaction TAP">Bonification/Réfaction TAP (pour les moûts)</option>
                        <option value="en autres prime">Autres</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="num" name="montant" style="width:100px; text-align: right; padding-right: 5px;" />
                </td>
                <td>
                    <select style="width:100px;margin:0px;" name="unite">
                        <option value="€ HT/Hl">€ HT/Hl</option>
                        <option value="€ HT/Kg">€ HT/Kg</option>
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
            $(".aideSaisiePrimesPopup").trigger("click");
        }
    });
    $("#popup_acheteur_primes_diverses").keypress(function(e) {if(e.which == 13) $("#popup_acheteur_primes_diverses .ajouter").trigger("click");});
    $("#popup_acheteur_primes_diverses .ajouter").click(function() {
        var type = $('#popup_acheteur_primes_diverses select[name="type"]').val();
        var montant = $('#popup_acheteur_primes_diverses input[name="montant"]').val();
        var unite = $('#popup_acheteur_primes_diverses select[name="unite"]').val();
        var ligne = montant+unite+' '+type;
        var contenu = $('#<?php echo $target ?>').val();
        if (contenu) {
            contenu += "\n";
        }
        if (montant) {
            $('#<?php echo $target ?>').val(contenu+ligne);
        }
        $("#popup_acheteur_primes_diverses a.close_popup").trigger("click");
        $('#popup_acheteur_primes_diverses select[name="type"]').val($('#popup_acheteur_primes_diverses select[name="type"] option:first').val());
        $('#popup_acheteur_primes_diverses input[name="montant"]').val('');
        $('#popup_acheteur_primes_diverses select[name="unite"]').val($('#popup_acheteur_primes_diverses select[name="unite"] option:first').val());
        return false;
    });
</script>
