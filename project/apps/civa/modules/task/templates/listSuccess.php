<h2 class="titre_principal">Tâches récurrentes</h2>

<div class="contenu">
        <div id="application_dr" class="clearfix">
            <?php foreach($tasks_container->getTasks() as $namespace => $tasks): ?>
            <div id="precedentes_declarations" style="width: 226px; margin: 5px;">
                <h3 class="titre_section"><?php echo preg_replace("/^[0-9]+-/", "", $namespace) ?></h3>
                <div class="contenu_section">
                    <ul>
                        <?php foreach($tasks as $task): ?>
                            <li><a class="tache-popup" href="<?php echo url_for('task_info', array('namespace' => $task->getNamespace(), 'slug' => $task->getSlug())) ?>"><?php echo $task->getName() ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endforeach ?>
        </div>
</div>
