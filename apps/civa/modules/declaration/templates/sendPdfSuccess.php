<?php if($emailSend == true){ ?>
        Votre déclaration de récolte vous a bien été envoyée par email.
<?php }else{ ?>
        Une erreur c'est produite lors de l'envoi de l'e-mail.<br /><br />Si le problème persiste merci de <a href="<?php echo url_for('contact'); ?>">contacter le CIVA</a>.
<?php } ?>