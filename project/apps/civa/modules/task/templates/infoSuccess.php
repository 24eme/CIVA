<div id="popup_confirme_validation" class="popup_ajout popup_confirme" title="Tâche">
    <h2 style="color: #848C03; font-weight: normal; font-size: 16px; text-align: center;"><?php echo $task->getNamespace(); ?>&nbsp;:&nbsp;<?php echo $task->getName(); ?></h2>
    <p style="margin-top: 10px;"><?php echo $info; ?></p>
    <div id="btns">
        <a class="tache-popup btn_majeur btn_petit btn_jaune" href="<?php echo url_for('task_run', array('namespace' => $task->getNamespace(), 'slug' => $task->getSlug())) ?>" data-loader="#popup_loader" >Exécuter</a>
    </div>
    
    <div style="display: none" id="popup_loader" title="Exécution">
        <h2 style="color: #848C03; font-weight: normal; font-size: 16px; text-align: center;"><?php echo $task->getNamespace(); ?>&nbsp;:&nbsp;<?php echo $task->getName(); ?></h2>
        <div class="popup-loading">

            <p>L'exécution de la tâche est cours.<br />Merci de patienter.<br /><small>La procédure peut prendre un peu de temps</small></p>
        </div>
    </div>
</div>