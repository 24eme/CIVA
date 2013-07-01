<?php if ($sf_user->getTiers()->isDeclarantStock()): ?>
    <div id="espace_alsace_recolte">
        <h2>Alsace Stocks</h2>
        <div class="contenu clearfix">  
            <?php include_component('ds', 'monEspace') ?>
            <?php include_component('ds', 'monEspaceColonne') ?>
        </div>
    </div>
<?php endif; ?>