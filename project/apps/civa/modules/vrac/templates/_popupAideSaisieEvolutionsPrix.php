<div id="popup_clause_evolution_prix" class="popup_ajout" title="Révision du prix pour les années N+1 et N+2">
    <p style="margin-bottom: 10px;">
        Choix des indicateurs d'évolution de prix au cours de ce contrat triennal(*).<br /><strong>Méthode de calcul</strong> :<br />prix de base du raisin x indice année n+1 / indice année N
    </p>
    <table>
        <tr>
            <th align="right">Indicateur :</th>
            <td align="left">
                <select style="width:225px;margin-bottom:5px" name="indicateur">
                    <option value="du pourcentage d'évolution du prix librement convenu entre les parties">Pourcentage d'évolution du prix librement convenu entre les parties</option>
                    <option value="de l'indicateur IPAMPA">IPAMPA.</option>
                    <option value="de l'indicateur IPAP">IPAP.</option>
                    <option value="de l'indicateur SMIC">SMIC.</option>
                    <option value="de l'indicateur d'évolution des stocks par cépage">Evolution des stocks par cépage</option>
                    <option value="de l'indicateur de dynamique commerciale par cépage">Dynamique commerciale par cépage</option>
                </select>
            </td>
        </tr>
        <tr>
            <th align="right">Part de l'indicateur :</th>
            <td align="left">
                <input required type="text" name="part" class="num" style="width:100px; text-align: right; padding-right: 5px;" />&nbsp;%
            </td>
        </tr>
        <tr>
            <th align="right">&nbsp;</th>
            <td align="left" style="width:225px;">
                <i style="font-size:85%;">Pourcentage de cet indice pris en compte dans le calcul de l'indice global d'évolution du prix de base. (<strong>la somme</strong> des parts des indicateurs choisis <strong>doit être égale à 100%</strong>).</i>
            </td>
        </tr>
    </table>
    <p style="margin: 10px 0;">
        Les indicateurs ainsi que la méthode de calcul du prix, basé sur ces indicateurs resteront les mêmes sur l'ensemble de la période contractualisée (Année N, N+1 et N+2).
    </p>
    <i style="font-size:85%;">(*) Indicateurs de prix et d'évolution des prix : les indicateurs pouvant être pris en compte sont ceux relatifs aux couts pertinents de production : indice IPAMPA (indice des prix d'achat des moyens de production agricole), IPAP vin (indice des prix des produits agricoles à la production), SMIC, pourcentage d'évolution librement convenu entre les parties, évolution des disponibilités de stocks par cépage et/ou de l'évolution commerciale globale constatés par l'interprofession.</i>
    <div class="clearfix" style="text-align: center; margin-top: 15px;">
        <input type="image" src="/images/boutons/btn_valider.png" alt="Ajouter" name="boutons[next]" class="ajouter" />
        <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
    </div>
</div>
<script type="text/javascript">
    $('#<?php echo $target ?>').focus(function() {
        if (!$('#<?php echo $target ?>').val()) {
            $(".aideSaisieEvolutionsPrixPopup").trigger("click");
        }
    });
    $("#popup_clause_evolution_prix .ajouter").click(function() {
        var indicateur = $('#popup_clause_evolution_prix select[name="indicateur"]').val();
        var part = $('#popup_clause_evolution_prix input[name="part"]').val();
        if (!part) {
            return;
        }
        var ligne = part+'% '+indicateur;
        var contenu = $('#<?php echo $target ?>').val();
        var lignes = contenu.split("\n");
        var total = 0;
        lignes.forEach(function(item){
            if (item.indexOf('%') >= 0)
                total += parseInt(item.substring(0, item.indexOf('%')));
        });
        total += parseInt(part);
        if (contenu) {
            contenu += "\n";
        }
        $('#<?php echo $target ?>').val(contenu+ligne);
        $("#popup_clause_evolution_prix a.close_popup").trigger("click");
        $("#partTotale").text(total);
        $("#partTotale").parent('p').show();
        $('#popup_clause_evolution_prix select[name="indicateur"]').val($('#popup_clause_evolution_prix select[name="indicateur"] option:first').val());
        $('#popup_clause_evolution_prix input[name="part"]').val('');
        return false;
    });
    $(".inputCleaner").click(function() {
        $("#partTotale").text(0);
        $("#partTotale").parent('p').hide();
    });
</script>
