<h3><?php echo $title ?></h3>
<ul>
    <?php foreach ($acheteurs as $cvi): ?>
    <?php include_partial('headerAcheteursItem', array('name' => $list_acheteurs[$cvi]['nom'], 'css_class' => 'acheteur_'.$acheteurs->getKey().'_'.$cvi)) ?>
    <?php endforeach; ?>
</ul>
