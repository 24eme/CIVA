<ul id="btn_etape" class="btn_prev_suiv clearfix">
    <!-- InstanceBeginEditable name="btn_etape" -->
    <li class="prec"><a href="<?php echo url_for('@exploitation_lieu') ?>" class="<?php if ($inactif): ?>btn_inactif<?php endif; ?>"><img src="/images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" /></a></li>
    <li class="suiv"><a href="<?php echo url_for('@exploitation_autres') ?>" class="<?php if ($inactif): ?>btn_inactif<?php endif; ?>"><img src="/images/boutons/btn_passer_etape_suiv.png" alt="Passer à l'étape suivante" /></a></li>
    <!-- InstanceEndEditable -->
</ul>