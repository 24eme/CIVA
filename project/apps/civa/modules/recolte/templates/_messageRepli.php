<?php

$cepage_assemblage = false;
foreach ( $onglets->getCurrentCepage()->getConfig()->getParent()->filter('^cepage') as $cepage ) {
    if( $cepage->key == 'cepage_ED' )
        $cepage_assemblage = true;
}

if ( $onglets->getCurrentCepage()->dplc > 0
  && $cepage_assemblage == true
  && $onglets->getCurrentCepage()->getConfig()->key != 'cepage_ED' ) : ?>

    <fieldset class='message' id="message_repli">
      <legend class="message_title">
         Information
      </legend>
      <p>Vous pouvez replier jusqu'à <?php echo $onglets->getCurrentCouleur()->dplc ?> hl en assemblage pour <?php echo $onglets->getCurrentLieu()->getParent()->getConfig()->libelle.' '.$onglets->getCurrentCepage()->getConfig()->libelle; ?></p>
    </fieldset>

<?php endif; ?>