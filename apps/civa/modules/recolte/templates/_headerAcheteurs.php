<h3><?php echo $title ?> <span class="unites">(hl)</span> <a href="" class="msg_aide" rel="<?php echo $var_rel_help; ?>" title="Message aide"></a></h3>
<?php if (count($acheteurs) > 0): ?>
<ul class="acheteurs">
    <?php foreach ($acheteurs as $cvi): ?>
    <?php include_partial('headerAcheteursItem', array('name' => $list_acheteurs[$cvi]['nom'], 'css_class' => 'acheteur_'.$acheteurs->getKey().'_'.$cvi)) ?>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
