<ul>
    <?php foreach ($acheteurs as $cvi): ?>
    <?php if (isset($acheteurs_value[$cvi])): ?>
    <?php include_partial('itemAcheteursItem', array('value' => $acheteurs_value[$cvi])) ?>
    <?php else: ?>
    <?php include_partial('itemAcheteursItem', array('value' => null)) ?>
    <?php endif; ?>
    <?php endforeach; ?>
</ul>
