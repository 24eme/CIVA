<div id="popup_ds_neant" class="popup_ajout" title="Action impossible">
        <p>
            Il est impossible de faire une déclaration de Stocks Néant. <br /><br />
            <?php if($hasVolume):?>
            Pour effectuer cette opération il faut retirer l'ensemble des volumes saisis dans la partie "Stocks" puis décocher toutes les appellations y compris les Vins Sans IG.<br />
            <?php else:?>
            Pour effectuer cette opération il faut décocher l'ensemble des appellations cochées y compris les Vins Sans IG.<br />
            <?php endif;?>
        </p>
</div>
