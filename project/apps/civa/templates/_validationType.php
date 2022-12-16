<ul class="messages_log">
    <?php foreach ($points as $controle): ?>
        <li>
            <?php if($afficheLiens && $controle->getRawValue()->getLien() && $controle->getRawValue()->getInfo()) :?>
            <?php echo $controle->getRawValue()->getMessage() ?> : <a href="<?php echo $controle->getRawValue()->getLien() ?>">
            <?php echo $controle->getRawValue()->getInfo() ?></a>
            <?php elseif($afficheLiens && $controle->getRawValue()->getLien()): ?>
                <a href="<?php echo $controle->getRawValue()->getLien() ?>">
                    <?php echo $controle->getRawValue()->getMessage() ?></a>
                </a>
            <?php else: ?>
            <?php echo $controle->getRawValue() ?>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>