<ul id="actions_declaration">
<?php if($etape == 3): ?>
        <li class="btn"><a href="" class="btn_voir_dr_prec"><img src="/images/boutons/btn_voir_dr_prec.png" alt="Voir les DR précedentes" /></a></li>

    <?php endif; ?>
    <?php if(isset($help_popup_action)): ?>
        <li><a href="" class="msg_aide" rel="<?php echo $help_popup_action ?>" title="Message aide"></a></li>

    <?php endif; ?>
</ul>
