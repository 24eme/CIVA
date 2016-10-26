<ul id="btn_appelation" class="btn_prev_suiv clearfix">
    <?php $margin = "190px"; ?>
    <li class="prec">
        <a href="<?php echo url_for('dr_recolte_noeud_precedent', array('sf_subject' => $produit, 'hash' => $produit->getLieu()->getHash())) ?>" class="btn_recolte_can_be_inactif" <?php if (isset($is_recap) && $is_recap): ?>onclick="document.getElementById('principal').submit(); return false;"<?php endif; ?>>
            <img src="/images/boutons/btn_appelation_prec.png" alt="Retour à l'appellation précédente" />
        </a>
    </li>
    <li class="previsualiser" style="<?php if (isset($margin)) { echo "margin-left: $margin;"; } ?>" >
        <a href="" class="open_popup_rendements_max">
            <img src="/images/boutons/btn_rendements_maxs.png" alt="Voir les rendements maximumms autorisés" />
        </a>
    </li>
    <li class="suiv">
        <?php if (isset($is_recap) && $is_recap): ?>
        <a href="" onclick="if (valider_can_submit()) { document.getElementById('principal').submit(); } return false;">
            <?php else: ?>
            <a href="<?php echo url_for('dr_recolte_recapitulatif', array('sf_subject' => $produit, "hash" => $produit->getLieu()->getHash())) ?>" class="btn_recolte_can_be_inactif">
                <?php endif; ?>
                <img src="/images/boutons/btn_appelation_suiv.png" alt="Valider et Passer à l'appellation suivante" />
            </a>
    </li>
</ul>
