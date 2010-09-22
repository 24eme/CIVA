<div id="<?php echo $id ?>" class="popup_ajout" title="<?php echo $title ?>">
    <form action="<?php echo $action ?>" method="post">

        <input type="hidden" name="type_cssclass" value="<?php echo $cssclass ?>" />
        <input type="hidden" name="type_name_field" value="<?php echo $name ?>" />

        <label for="champ_acheteur_nom">Entrez le nom de l'acheteur, son CVI ou sa commune :</label>
        <input id="champ_acheteur_nom" class="nom" type="text" name="" />
        <input class="cvi" type="hidden" name="" />
        <input class="commune" type="hidden" name="" />
        <input type="image" name="" src="/images/boutons/btn_valider.png" alt="Valider" />
        <span class="valider-loading"></span>
    </form>
    <div class="close_btn"><a class="close_popup" href=""><img alt="Fermer la fenetre" src="/images/boutons/btn_fermer.png"></a></div>
</div>