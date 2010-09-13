<h3><?php echo $title ?> <a href="" class="msg_aide" rel="<?php echo $var_rel_help; ?>" title="Message aide">Test message d'aide</a></h3>
<ul class="acheteurs">
    <?php foreach ($acheteurs as $cvi): ?>
    <?php include_partial('headerAcheteursItem', array('name' => $list_acheteurs[$cvi]['nom'], 'css_class' => 'acheteur_'.$acheteurs->getKey().'_'.$cvi)) ?>
    <?php endforeach; ?>
</ul>
