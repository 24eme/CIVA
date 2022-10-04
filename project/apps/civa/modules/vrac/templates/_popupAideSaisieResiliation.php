<div id="popup_clause_resiliation" class="popup_ajout" title="En cas de résiliation du contrat">
    <table>
        <tr>
            <th align="right">Cas de résiliation :</th>
            <td align="left">
                <input type="text" name="resiliation" style="width:225px;margin-bottom:5px;" />
            </td>
        </tr>
        <tr>
            <th align="right">Délai de préavis :</th>
            <td align="left">
                <input type="text" name="preavis" style="width:225px;margin-bottom:5px;" />
            </td>
        </tr>
        <tr>
            <th align="right">Indemnités :</th>
            <td align="left">
                <input type="text" name="indemnites" style="width:225px;margin-bottom:5px;" />
            </td>
        </tr>
    </table>
    <div class="clearfix" style="text-align: center; margin-top: 15px;">
        <input type="image" src="/images/boutons/btn_valider.png" alt="Ajouter" name="boutons[next]" class="ajouter" />
        <a class="close_popup" href=""><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
    </div>
</div>
<script type="text/javascript">
    $('#<?php echo $target ?>').focus(function() {
        if (!$('#<?php echo $target ?>').val()) {
            $(".aideSaisieResiliationPopup").trigger("click");
        }
    });
    $("#popup_clause_resiliation .ajouter").click(function() {
        var resiliation = $('#popup_clause_resiliation input[name="resiliation"]').val();
        var preavis = $('#popup_clause_resiliation input[name="preavis"]').val();
        var indemnites = $('#popup_clause_resiliation input[name="indemnites"]').val();
        var ligne = 'Cas de résiliation : '+resiliation+'\nDélai de préavis : '+preavis+'\nIndemnités : '+indemnites;
        $('#<?php echo $target ?>').val(ligne);
        $("#popup_clause_resiliation a.close_popup").trigger("click");
        $('#popup_clause_resiliation input[name="resiliation"]').val('');
        $('#popup_clause_resiliation input[name="preavis"]').val('');
        $('#popup_clause_resiliation input[name="indemnites"]').val('');
        return false;
    });
</script>
