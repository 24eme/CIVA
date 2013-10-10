<?php include_partial('vrac/etapes', array('vrac' => $vrac, 'etapes' => $etapes, 'current' => $etape)) ?>

<?php if ($vrac->isSupprimable($user->_id)): ?>
	<div class="btn_header">
		<a id="btn_precedent" href="<?php echo url_for('vrac_supprimer', $vrac) ?>">
			<img alt="Retourner à l'étape précédente" src="/images/boutons/btn_supprimer_contrat.png">
		</a>
	</div>
<?php endif; ?>

<div id="contrats_vrac">

    <form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'etape' => $etape)) ?>">
        <?php echo $form->renderHiddenFields() ?>
        <?php echo $form->renderGlobalErrors() ?>
        <?php include_partial('form_'.$etape, array('form' => $form, 'vrac' => $vrac, 'etape' => $etape)) ?>

        <ul class="btn_prev_suiv clearfix" id="btn_etape">
            <li class="prec">
                <a id="btn_precedent" href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'etape' => $etapes->getPrev($etape))) ?>">
                    <img alt="Retourner à l'étape précédente" src="/images/boutons/btn_retourner_etape_prec.png">
                </a>
            </li>
            <li class="suiv">
                <?php if ($etapes->getLast() == $etape): ?>     
                <button type="submit" name="valider" style="cursor: pointer;">
                    <img alt="Continuer à l'étape suivante" src="/images/boutons/btn_valider.png" />
                </button>
                <?php else: ?>
                <button type="submit" name="valider" style="cursor: pointer;">
                    <img alt="Continuer à l'étape suivante" src="/images/boutons/btn_passer_etape_suiv.png" />
                </button>
                <?php endif; ?>
            </li>
        </ul>
    </form>
</div>

