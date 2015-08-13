<?php use_helper('Bash') ?>

<div id="popup_confirme_validation" class="popup_ajout popup_confirme popup_tache" title="<?php echo preg_replace("/^[0-9]+-/", "", $task->getNamespace()) ?>">
    <h2 style="color: #848C03; font-weight: normal; font-size: 16px; text-align: center;"><?php echo $task->getName(); ?></h2>
        <div class="output">
        <?php if($result): ?>
            <?php echo format_bash_result($result); ?>
        <?php else: ?>
            <p style="margin-top: 10px; text-align: center; font-style: italic">La tâche a bien été exécutée, mais n'a rien retournée.</p>
        <?php endif; ?>
        </div>

        <div class="btns">
            <?php echo extract_link($result); ?>
        </div>
</div>