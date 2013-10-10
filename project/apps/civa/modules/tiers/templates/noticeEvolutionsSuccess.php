<h2 class="titre_principal">DECLARATION DE RECOLTE <?php echo $campagne; ?> – LES NOUVEAUTES</h2>
    <div id="notice_evolutions"><?php echo acCouchdbManager::getClient('Messages')->getMessage('notice_evolutions_'.$campagne); ?>
        <a href="<?php echo url_for('@mon_espace_civa') ?>"><img src="/images/boutons/btn_fermer.png"  alt="Fermer " /></img></a>
</div>
