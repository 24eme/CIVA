<ul id="etape_declaration" class="clearfix">
    <li class="<?php if ($etape > 1): ?>passe<?php elseif($etape == 1): ?>actif<?php endif; ?>">Exploitation <em>Etape 1</em></li>
    <li class="<?php if ($etape > 2): ?>passe<?php elseif($etape == 2): ?>actif<?php endif; ?>">Récolte <em>Etape 2</em></li>
    <li class="<?php if ($etape > 3): ?>passe<?php elseif($etape == 3): ?>actif<?php endif; ?>">Validation <em>Etape 3</em></li>
</ul>