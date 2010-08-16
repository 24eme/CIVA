					<ul id="btn_appelation" class="btn_prev_suiv clearfix">
                                                <?php if ($onglets->hasPreviousAppellation()): ?>
                                                    <li class="prec"><a href="<?php echo url_for($onglets->getPreviousUrl()->getRawValue()) ?>"><img src="/images/boutons/btn_appelation_prec.png" alt="Retour à l'appelation précédente" /></a></li>
                                                <?php endif; ?>
						<li class="suiv"><a href="<?php echo 
if (isset($is_recap) && $is_recap) {
url_for($onglets->getNextUrl()->getRawValue());
}else{
url_for($onglets->getUrlRecap()->getRawValue());
}
 ?>"><img src="/images/boutons/btn_appelation_suiv.png" alt="Valider et Passer à l'appelation suivante" /></a></li>
					</ul>
