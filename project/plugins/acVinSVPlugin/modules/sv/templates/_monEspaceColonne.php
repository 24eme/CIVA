<div id="precedentes_declarations">
    <h3 class="titre_section">
        Historique des déclarations<a class="msg_aide_ds" href="" title="Message aide"></a>
    </h3>
    <div class="contenu_section">
        <ul class="bloc_vert">
            <li>
                <a href="#">Années précédentes</a>
                <ul class="declarations">
                    <?php foreach ($svs as $sv): ?>
                        <?php if(substr($sv, -4, 4) == CurrentClient::getCurrent()->campagne): continue; endif; ?>
                        <li>
                            <a href="<?php echo url_for('sv_visualisation', ['id' => $sv]) ?>">
                                <?php echo substr($sv, -4, 4) ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </li>
        </ul>
    </div>
</div>
