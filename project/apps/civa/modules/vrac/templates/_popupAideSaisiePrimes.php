<div id="popup_acheteur_primes_diverses" class="popup_ajout" title="Primes diverses à la charge de l’acheteur">
    <form method="post" action="">
        <table>
            <thead>
                <tr>
                    <th>Type de frais</th>
                    <th>Montant (€ HT)</th>
                    <th>Unité</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select style="width:120px;margin:0px;" name="type">
                            <option value="Apport global :">Apport global</option>
                            <option value="Engagement surface / volume :">Engagement surface / volume</option>
                            <option value="Vendange Manuelle :">Vendange Manuelle</option>
                            <option value="Autre :">Autre</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="montant" style="width:100px;" />
                    </td>
                    <td>
                        <select style="width:100px;margin:0px;" name="unite">
                            <option value="par Hl">Hl</option>
                            <option value="pourcent">en %</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="clearfix" style="text-align: center; margin-top: 15px;">
            <input type="image" src="/images/boutons/btn_valider.png" alt="Ajouter" name="boutons[next]" class="ajouter" />
            <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
        </div>
    </form>
</div>
<script type="text/javascript">
    $("#popup_acheteur_primes_diverses .ajouter").click(function() {
        var type = $('#popup_acheteur_primes_diverses select[name="type"]').val();
        var montant = $('#popup_acheteur_primes_diverses input[name="montant"]').val();
        if (!montant) {
            montant = 0;
        }
        var unite = $('#popup_acheteur_primes_diverses select[name="unite"]').val();
        var ligne = type+' '+montant+'€ HT '+unite;
        var content = $('#<?php echo $target ?>').val();
        if (content) {
            content += "\n";
        }
        $('#<?php echo $target ?>').val(content+ligne);
        $('#<?php echo $target ?>').removeAttr('disabled');
        $("#popup_acheteur_primes_diverses a.close_popup").trigger("click");
        $('#popup_acheteur_primes_diverses select[name="type"]').val($('#popup_acheteur_primes_diverses select[name="type"] option:first').val());
        $('#popup_acheteur_primes_diverses input[name="montant"]').val('');
        $('#popup_acheteur_primes_diverses select[name="unite"]').val($('#popup_acheteur_primes_diverses select[name="unite"] option:first').val());
        return false;
    });
</script>
