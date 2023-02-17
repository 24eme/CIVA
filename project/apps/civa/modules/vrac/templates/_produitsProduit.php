<td class="produit <?php echo isVersionnerCssClass($detail, 'millesime') ?> <?php echo isVersionnerCssClass($detail, 'denomination') ?> <?php echo isVersionnerCssClass($detail, 'label') ?> <?php echo isVersionnerCssClass($detail, 'actif') ?>">
    <?php echo $detail->getLibelleSansCepage(); ?> <strong>
        <?php echo $detail->getLieuLibelle(); ?> <?php echo $detail->getCepage()->getLibelle(); ?> <?php echo $detail->getComplementPartielLibelle(); ?>  <?php echo $detail->millesime; ?> <?php echo $detail->denomination; ?></strong><?php echo ($detail->exist('label') && $detail->get("label"))? " ".VracClient::$label_libelles[$detail->get("label")] : ""; ?>
    <?php if(isset($produits_hash_in_error) && in_array($detail->getHash(), $produits_hash_in_error->getRawValue())): ?>
        <img src="/images/pictos/pi_alerte.png" alt="" />
    <?php endif; ?>
</td>
