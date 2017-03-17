<?php include_partial('vrac/etapes', array('vrac' => $vrac, 'etapes' => $etapes, 'current' => $etape)) ?>

<ul id="onglets_majeurs" class="clearfix">
	<li class="ui-tabs-selected">
		<a href="#" style="height: 18px;"><?php echo $etapes->getLibelle($etape) ?></a>
	</li>
</ul>
<div id="contrats_vrac">

    <form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'etape' => $etape)) ?>">


        <div class="fond">
        	<?php
				if($validation->hasPoints() && !$next_etape) {
					include_partial('global/validation', array('validation' => $validation));
				}
			?>
            <?php echo $form->renderHiddenFields() ?>
            <?php echo $form->renderGlobalErrors() ?>
            <?php include_partial('form_'.$etape, array('form' => $form, 'vrac' => $vrac, 'etape' => $etape, 'referer' => $referer, 'user' => $user)) ?>
        </div>

        <ul class="btn_prev_suiv clearfix" id="btn_etape">
            <li class="prec">
            	<?php if ($etapes->getFirst() != $etape): ?>
                <a id="btn_precedent" href="<?php echo url_for('vrac_etape', array('sf_subject' => $vrac, 'etape' => $etapes->getPrev($etape))) ?>">
                    <img alt="Retourner à l'étape précédente" src="/images/boutons/btn_retourner_etape_prec.png">
                </a>
                <?php endif; ?>
            </li>
            <?php if(!$validation->hasErreurs() || $next_etape): ?>
            <li class="suiv">
                <?php if ($etapes->getLast() == $etape): ?>
                 <input tabindex="1" type="image" src="/images/boutons/btn_valider_final.png" alt="Valider votre contrat" id="valideVrac" />
                <?php else: ?>
                <button type="submit" name="valider" style="cursor: pointer;">
                    <img alt="Continuer à l'étape suivante" src="/images/boutons/btn_passer_etape_suiv.png" />
                </button>
                <?php endif; ?>
            </li>
            <?php endif; ?>
        </ul>
    </form>
</div>
