<ul>
    <li style="display:none"></li>
    <?php foreach ($acheteurs as $cvi): ?>
    <?php if (isset($acheteurs_value[$cvi])): ?>
    <?php include_partial('itemAcheteursItem', array('value' => $acheteurs_value[$cvi], 'css_class' => 'acheteur_'.$acheteurs->getKey().'_'.$cvi)) ?>
    <?php else: ?>
    <?php include_partial('itemAcheteursItem', array('value' => null, 'css_class' => 'acheteur_'.$acheteurs->getKey().'_'.$cvi)) ?>
    <?php endif; ?>
    <?php endforeach; ?>
</ul>
