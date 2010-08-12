<h3><?php echo $title ?></h3>
<?php if ($acheteurs->count() > 0): ?>
    <ul>
    <?php foreach ($acheteurs as $cvi): ?>
        <li>
            <?php if (isset($acheteurs_value[$cvi])): ?>
                <?php echo $acheteurs_value[$cvi] ?>
            <?php else: ?>
                &nbsp;
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>