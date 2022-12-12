<script type="text/javascript">
    ajax_url_to_process_send_acheteur = "<?php echo url_for('dr_send_email_acheteurs_pdf', array('id' => $dr->_id, 'annee' => $annee, 'message' => 'custom')); ?>;";

    var sendMailAcheteurs = function(){
            openPopup($('#popup_loader_send'));
            $.ajax({
                url: ajax_url_to_process_send_acheteur ,
                success: function(data) {
                    $('.popup-loading').empty();
                    $('.popup-loading').css('background', 'none');
                    $('.popup-loading').css('padding-top', '10px');
                    $('.popup-loading').append('<p>' + data + '</p>');
                    $('#popup_confirme_mail_acheteur').dialog('close');

                }
            });
        };
    </script>

<div id="popup_confirme_mail_acheteur" class="popup_ajout popup_confirme" title="Confirmation d'envoi de mail de votre DR">
    <form method="post" action="">
        <p>
            Confirmez-vous l'envoi par mail aux acheteurs de votre Déclaration de Récolte&nbsp;?<br />
        </p>
        <div id="btns">
            <input onclick="sendMailAcheteurs()" type="image" src="/images/boutons/btn_valider.png" alt="Valider votre déclaration" name="boutons[next]" id="valideDR" class="valideDR_OK" />
            <a class="close_popup" href=""  onclick="$('#popup_confirmation').dialog('close');" ><img alt="Annuler" src="/images/boutons/btn_annuler.png"></a>
        </div>
    </form>
</div>


<div style="display: none" id="popup_loader_send" title="Envoie du PDF">
    <div class="popup-loading">
        <p>L'envoi par e-mail est en cours.<br/>Merci de patienter.<br/><small>La procédure peut prendre 30 secondes</small></p>
    </div>
</div>
