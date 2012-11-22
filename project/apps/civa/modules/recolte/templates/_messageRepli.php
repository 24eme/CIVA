<?php

$cepage_assemblage = false;
foreach ( $onglets->getCurrentCepage()->getConfig()->getParent()->filter('^cepage') as $cepage ) {
    if( $cepage->key == 'cepage_ED' && $cepage->getRendement() == -1  )
        $cepage_assemblage = true;
}

if (   $onglets->getCurrentCepage()->getDplc() > 0
    && $cepage_assemblage == true
    && $onglets->getCurrentCepage()->getConfig()->key != 'cepage_ED' ) : ?>

    <fieldset class='message' id="message_repli">
      <legend class="message_title">
         Information
      </legend>
      <p>Vous pouvez replier jusqu'à <?php echo $onglets->getCurrentCepage()->getDplc() ?> hl en assemblage pour ce cépage.</p>
    </fieldset>

<?php endif; ?>