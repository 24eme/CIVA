<?php use_helper('civa') ?>
<li>
    <input type="hidden" value="<?php if ($value) echo $value; else echo '0'; ?>" class="<?php echo $css_class ?>_orig" />
    <input type="text" readonly="readonly" value="<?php echoFloat($value); ?>" class="acheteur <?php echo $css_class ?>" />
</li>