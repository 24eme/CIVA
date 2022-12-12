<script type="text/javascript">
    ajax_url_to_process = "<?php echo url_for('ds_send_email_pdf',array('id'=> $ds->_id, 'message' => $message)); ?>";

    var sendMail = function(){
            openPopup($('#popup_loader_send'));
            $.ajax({
                url: ajax_url_to_process ,
                success: function(data) {
                    $('.popup-loading').empty();
                    $('.popup-loading').css('background', 'none');
                    $('.popup-loading').css('padding-top', '10px');
                    $('.popup-loading').append('<p>' + data + '</p>');
                    $('#popup_confirme_mail').dialog('close');

                }
            });
        };
    </script>

<div id="popup_confirme_mail" class="popup_ajout" title="Confirmation d'envoi de mail de votre DStocks">
    <form method="post" action="">
        <p>
            Confirmez-vous l'envoi par mail de votre déclaration de Stocks ?
        </p>
        <div id="btns" class="clearfix">
            <input onclick="sendMail()" type="image" src="/images/boutons/btn_valider.png" alt="Valider votre déclaration" name="boutons[next]" id="valideDR" class="valideDR_OK" />
            <a class="close_popup" href="" onclick="$('#popup_confirmation').dialog('close');"><img alt="Annuler" src="/images/boutons/btn_annuler.png" /></a>
        </div>
    </form>
</div>


<div style="display: none" id="popup_loader_send" title="Envoie du PDF">
    <div class="popup-loading">
        <p>L'envoi par e-mail de votre DS est en cours.<br/>Merci de patienter.<br/><small>La procédure peut prendre 30 secondes</small></p>
    </div>
</div>