<ul id="etape_declaration" class="clearfix">
    <li class="<?php if ($etape > 1): ?>passe<?php elseif($etape == 1): ?>actif<?php endif; ?>">
        <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ETAPE_EXPLOITATION)): ?>
            <a href="<?php echo url_for('@exploitation_administratif'); ?>">Exploitation <em>Etape 1</em></a>
        <?php else: ?>
            <a href="#" onclick="return false;">Exploitation <em>Etape 1</em></a>
        <?php endif; ?>
    </li>
    <li class="<?php if ($etape > 2): ?>passe<?php elseif($etape == 2): ?>actif<?php endif; ?>">
        <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ETAPE_RECOLTE)): ?>
            <a href="<?php echo url_for('@recolte'); ?>">Récolte <em>Etape 2</em></a>
        <?php else: ?>
             <a href="#" onclick="return false;">Récolte <em>Etape 2</em></a>
        <?php endif; ?>
    </li>
    <li class="<?php if ($etape > 3): ?>passe<?php elseif($etape == 3): ?>actif<?php endif; ?>">
        <?php if ($sf_user->hasCredential(myUser::CREDENTIAL_ETAPE_VALIDATION)): ?>
            <a href="<?php echo url_for('@validation'); ?>">Validation <em>Etape 3</em></a>
        <?php else: ?>
            <a href="#" onclick="return false;">Validation <em>Etape 3</em></a>
        <?php endif; ?>
    </li>
</ul>