<h2 class="titre_principal">Mon espace CIVA</h2>

<div id="application_dr" class="clearfix">
    <div id="nouvelle_declaration">
        <?php if($tiers->hasNoAssices()): ?>
            <?php include_partial('accesGamma') ?>
        <?php endif; ?>
    </div>
    <div id="precedentes_declarations">
        <?php if($tiers->hasNoAssices()): ?>
            <?php include_partial('helpGamma') ?>
        <?php endif; ?>
    </div>
</div>
