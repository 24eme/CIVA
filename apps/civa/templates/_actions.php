<!--<ul id="actions_declaration">
    <li><a href="#"><img src="/images/pictos/pi_loupe.png" alt="Rechercher" /></a></li>
    <li><a href="#"><img src="/images/pictos/pi_enregistrer.png" alt="Enregistrer" /></a></li>
    <li><a href="#"><img src="/images/pictos/pi_imprimer.png" alt="Imprimer" /></a></li>
</ul>-->

<ul id="actions_declaration">
    <?php if($etape == 2): ?><li class="btn"><a href="" class="btn_voir_dr_prec"><img src="/images/boutons/btn_voir_dr_prec.png" alt="Voir les DR précedentes" /></a></li><?php endif; ?>
    <?php if(isset($help_popup_action)): ?><li><a href="" class="msg_aide" rel="<?php echo $help_popup_action ?>" title="Message aide">Teste message d'aide</a></li><?php endif; ?>
</ul>