<?php

$cepage_assemblage = false;
foreach ( $produit->getConfig()->getParent()->filter('^cepage') as $cepage ) {
    if( $cepage->key == 'cepage_ED' && $cepage->getRendementCepage() == -1  )
        $cepage_assemblage = true;
}

if (   $produit->getDplc() > 0
    && $cepage_assemblage == true
    && $produit->getConfig()->key != 'cepage_ED' ) : ?>

    <fieldset class='message' id="message_repli">
      <legend class="message_title">
         Information
      </legend>
      <p>Vous pouvez replier jusqu'à <?php echo $produit->getDplc() ?> hl en assemblage pour ce cépage.</p>
    </fieldset>

<?php endif; ?>
