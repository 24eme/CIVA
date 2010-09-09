<ul id="etape_declaration" class="clearfix">
    <li class="<?php if ($etape > 1): ?>passe<?php elseif($etape == 1): ?>actif<?php endif; ?>"><a href="#">Exploitation <em>Etape 1</em></a></li>
    <li class="<?php if ($etape > 2): ?>passe<?php elseif($etape == 2): ?>actif<?php endif; ?>"><a href="#">Récolte <em>Etape 2</em></a></li>
    <li class="<?php if ($etape > 3): ?>passe<?php elseif($etape == 3): ?>actif<?php endif; ?>"><a href="#">Validation <em>Etape 3</em></a></li>
</ul>