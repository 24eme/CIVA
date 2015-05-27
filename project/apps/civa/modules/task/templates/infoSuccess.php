<?php use_helper('Bash') ?>

<div id="popup_confirme_validation" class="popup_ajout popup_confirme popup_tache" title="<?php echo preg_replace("/^[0-9]+-/", "", $task->getNamespace()) ?>">
    <h2><?php echo $task->getName(); ?></h2>
    <div class="input">
        <?php echo format_bash_result($info, ""); ?>
    </div>
    <div class="btns">
        <a class="tache-popup btn_majeur btn_petit btn_jaune" href="<?php echo url_for('task_run', array('namespace' => $task->getNamespace(), 'slug' => $task->getSlug())) ?>" data-loader="#popup_loader" >Exécuter</a>
    </div>
    <div style="display: none" id="popup_loader" class="popup_tache" title="<?php echo preg_replace("/^[0-9]+-/", "", $task->getNamespace()) ?>">
        <h2><?php echo $task->getName(); ?></h2>
        <div class="popup-loading input">
            <p>L'exécution de la tâche est cours.<br />Merci de patienter.<br /><small>La procédure peut prendre un peu de temps</small></p>
        </div>
    </div>
</div>