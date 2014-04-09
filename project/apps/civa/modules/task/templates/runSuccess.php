<?php use_helper('Bash') ?>
<?php use_helper('Text') ?>

<div id="popup_confirme_validation" class="popup_ajout popup_confirme" title="Résultat">
    <h2 style="color: #848C03; font-weight: normal; font-size: 16px; text-align: center;"><?php echo $task->getNamespace(); ?>&nbsp;:&nbsp;<?php echo $task->getName(); ?></h2>
    
        <?php if($result): ?>
            <p style="margin-top: 10px;"><?php echo format_bash_result($result); ?></p>
        <?php else: ?>
            <p style="margin-top: 10px; text-align: center; font-style: italic">La tâche a bien été exécutée, mais n'a rien retournée.</p>
        <?php endif; ?>
</div>