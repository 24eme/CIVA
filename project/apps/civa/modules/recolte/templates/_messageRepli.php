<?php if( $onglets->getCurrentCouleur()->dplc > 0 && $onglets->getCurrentCepage()->libelle != 'Rebêches' ) : ?>

<div id="message_repli">
  <p>Vous pouvez replier jusqu'à <?php echo $onglets->getCurrentCouleur()->dplc ?> hl en assemblage pour <?php echo $onglets->getCurrentLieu()->getParent()->getConfig()->libelle.' '.$onglets->getCurrentCouleur()->getConfig()->libelle; ?></p>
</div>

<?php endif ;?>