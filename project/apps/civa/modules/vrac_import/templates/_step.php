<nav class="navbar navbar-default nav-step">
    <ul class="nav navbar-nav">
        <li class="<?php if(in_array($step, ['import'])): ?>active<?php endif; ?> <?php if(in_array($step, ['annexes', 'validation'])): ?>visited disabled<?php endif; ?>">
            <a href="" class=""><span>Import du fichier</span><small class="hidden">Etape 1</small></a>
        </li>
        <li class="<?php if(in_array($step, ['annexes'])): ?>active<?php endif; ?> <?php if(!in_array($step, ['annexes', 'validation'])): ?>disabled<?php endif; ?> <?php if(in_array($step, ['validation'])): ?>visited<?php endif; ?>">
            <a href="<?php if(isset($csvVrac)): ?><?php echo url_for('vrac_csv_fiche', ['csvvrac' => $csvVrac->_id]); ?><?php endif; ?>" class=""><span>Annexes</span><small class="hidden">Etape 2</small></a>
        </li>
        <li class="<?php if(!in_array($step, ['validation'])): ?>disabled<?php endif; ?> <?php if(in_array($step, ['validation'])): ?>active<?php endif; ?>">
            <a href="<?php if(isset($csvVrac)): ?><?php echo url_for('vrac_csv_validation', ['csvvrac' => $csvVrac->_id]); ?><?php endif; ?>" class=""><span>Validation</span><small class="hidden">Etape 3</small></a>
        </li>
    </ul>
</nav>
