<?php use_helper('Bash') ?>
<?php use_helper('Text') ?>

<div id="popup_confirme_validation" class="popup_ajout popup_confirme" title="Résultat">
    <h2 style="color: #848C03; font-weight: normal; font-size: 16px; text-align: center;"><?php echo $task->getName(); ?></h2>
    <p style="margin-top: 10px;">
        <?php echo format_bash_result($result); ?>
    <p>
</div>