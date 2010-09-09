					<ul id="btn_appelation" class="btn_prev_suiv clearfix">
                                                <?php if ($onglets->hasPreviousAppellation()): ?>
                                                    <li class="prec">
                                                        <a href="<?php echo url_for($onglets->getPreviousUrl()->getRawValue()) ?>" class="btn_recolte_can_be_inactif" <?php if (isset($is_recap) && $is_recap): ?>onclick="document.getElementById('principal').submit(); return false;"<?php endif; ?>>
                                                                        <img src="/images/boutons/btn_appelation_prec.png" alt="Retour à l'appelation précédente" />
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                    <li class="suiv">
                                                    <?php if (isset($is_recap) && $is_recap): ?>
                                                            <a href="<?php echo url_for($onglets->getNextUrl()->getRawValue()) ?>" onclick="document.getElementById('principal').submit(); return false;">
                                                        <?php else: ?>
                                                            <a href="<?php echo url_for($onglets->getUrlRecap(true)->getRawValue()) ?>" class="btn_recolte_can_be_inactif">
                                                        <?php endif; ?>
                                                                <img src="/images/boutons/btn_appelation_suiv.png" alt="Valider et Passer à l'appelation suivante" />
                                                            </a>
                                                    </li>
                                         </ul>
