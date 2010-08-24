<form action="<?php echo url_for(array_merge($onglets->getUrl('recolte_motif_non_recolte')->getRawValue(), array('detail_key' => $detail_key))) ?>" method="post">
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <?php echo $form['motif_non_recolte']->renderError(); ?>
    <?php echo $form['motif_non_recolte']->renderLabel(); ?>
    <?php echo $form['motif_non_recolte']->render(); ?>

    <input type="submit" value="Valider" />
</form>