<?php use_helper('Float') ?>
<li>
    <input type="hidden" value="<?php if ($value) echo $value; else echo '0'; ?>" class="<?php echo $css_class ?>_orig" />
    <input data-form-input-id="detail_<?php echo str_replace('acheteur_', '', $css_class) ?>_quantite_vendue" type="text" readonly="readonly" value="<?php echoFloat($value); ?>" class="num acheteur <?php echo $css_class ?>" />
</li>
