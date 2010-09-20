<div id="popup_dr_precedentes" class="popup_ajout" title="Déclarations précedentes">
    <p>Choisisser la déclaration que vous souhaitez visualiser :</p>
    <?php if (count($campagnes) > 0): ?>
    <ul class="declarations">
            <?php foreach ($campagnes as $id => $campagne): ?>
        <!--<li>
            <a href="#"><?php echo $campagne ?></a>
            <ul>-->
        <li><?php echo link_to($campagne, '@visualisation?annee='.$campagne, array('target'=>'blank')); ?></li>
        <!-- </ul>
     </li>-->
            <?php endforeach; ?>
    </ul>
    <?php endif; ?>
    <div class="close_btn"><a class="close_popup" href=""><img alt="Fermer la fenetre" src="/images/boutons/btn_fermer.png"></a></div>
</div>
