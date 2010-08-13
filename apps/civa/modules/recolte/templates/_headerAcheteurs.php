<h3><?php echo $title ?></h3>
<?php if ($acheteurs->count() > 0): ?>
            <ul>
    <?php foreach ($acheteurs as $cvi): ?>
                <li><?php echo $list_acheteurs[$cvi]['nom'] ?></li>
    <?php endforeach; ?>
            </ul>
<?php endif; ?>