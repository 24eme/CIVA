<?php

$cepage_assemblage = false;
foreach ($produit->getConfig()->getParentNode()->getCepages() as $cepage ) {
    if($cepage->getKey() == 'ED' && $cepage->getRendementCepage() == -1) {
        $cepage_assemblage = true;
    }
}

if (($produit->getDplc() - $produit->getLies()) > 0 && $cepage_assemblage == true && $produit->getConfig()->getKey() != 'ED' ) : ?>
    <fieldset class='message' id="message_repli">
      <legend class="message_title">
         Information
      </legend>
      <p>Vous pouvez replier jusqu'à <?php echo ($produit->getDplc() - $produit->getLies()) ?> hl en assemblage pour ce cépage.</p>
    </fieldset>
<?php endif; ?>
