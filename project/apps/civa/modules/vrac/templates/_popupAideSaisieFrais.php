<div id="popup_vendeur_frais_annexes" class="popup_ajout" title="Frais annexes en sus à la charge du vendeur">
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
                            <option value="Transport :">Transport</option>
                            <option value="Cotisation CIVA :">Cotisation CIVA</option>
                            <option value="Courtage :">Courtage</option>
                            <option value="Autre :">Autre</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="montant" style="width:100px;" />
                    </td>
                    <td>
                        <select style="width:100px;margin:0px;" name="unite">
                            <option value="par Hl">Hl</option>
                            <option value="au forfait">au forfait</option>
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
    $("#popup_vendeur_frais_annexes .ajouter").click(function() {
        var type = $('#popup_vendeur_frais_annexes select[name="type"]').val();
        var montant = $('#popup_vendeur_frais_annexes input[name="montant"]').val();
        if (!montant) {
            montant = 0;
        }
        var unite = $('#popup_vendeur_frais_annexes select[name="unite"]').val();
        var ligne = type+' '+montant+'€ HT '+unite;
        var content = $('#<?php echo $target ?>').val();
        if (content) {
            content += "\n";
        }
        $("#popup_vendeur_frais_annexes a.close_popup").trigger("click");
        $('#popup_vendeur_frais_annexes select[name="type"]').val($('#popup_vendeur_frais_annexes select[name="type"] option:first').val());
        $('#popup_vendeur_frais_annexes input[name="montant"]').val('');
        $('#popup_vendeur_frais_annexes select[name="unite"]').val($('#popup_vendeur_frais_annexes select[name="unite"] option:first').val());
        return false;
    });
</script>
