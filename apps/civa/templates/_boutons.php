<ul id="btn_etape" class="btn_prev_suiv clearfix">
        <?php if(in_array('precedent', $display->getRawValue())): ?>
            <li class="prec"><input type="image" src="../images/boutons/btn_retourner_etape_prec.png" alt="Retourner à l'étape précédente" name="retourner_etape" /></li>
        <?php endif; ?>
        <?php if(in_array('previsualiser', $display->getRawValue())): ?>
            <li class="previsualiser"><input type="image" src="../images/boutons/btn_previsualiser.png" alt="Prévisualiser" name="previsualiser" /></li>
        <?php endif; ?>
        <?php if(in_array('suivant', $display->getRawValue())): ?>
            <li class="suiv"><input type="image" src="../images/boutons/btn_passer_etape_suiv.png" alt="Passer à l'étape suivante" name="passer_etape" /></li>
        <?php endif; ?>
</ul>