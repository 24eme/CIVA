<?php if ($acheteurs->count() > 0): ?>
    <ul>
    <?php foreach ($acheteurs as $cvi): ?>
        <li>
            <input type="text" readonly="readonly" value="<?php if (isset($acheteurs_value[$cvi])): echo $acheteurs_value[$cvi]; endif; ?>" />
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>