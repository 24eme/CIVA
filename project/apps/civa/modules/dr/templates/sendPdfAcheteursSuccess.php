<?php if($sendMailAcheteursReport): ?>
        Votre déclaration de récolte a bien été envoyée par email aux acheteurs suivant :
        <ul>
        <?php foreach ($sendMailAcheteursReport as $acheteurReport) : ?>
            <li><?php echo $acheteurReport->type; ?>, <?php echo $acheteurReport->nom ?>, <?php echo $acheteurReport->cvi; ?></li>
        <?php endforeach; ?>
        </ul>
       
<?php else: ?>
        Une erreur c'est produite lors de l'envoi de l'e-mail.<br /><br />Si le problème persiste merci de <a href="<?php echo url_for('contact'); ?>">contacter le CIVA</a>.
<?php endif; ?>