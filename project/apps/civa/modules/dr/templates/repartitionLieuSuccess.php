<?php include_partial('dr/etapes', array('etape' => 2, 'dr' => $dr)) ?>
<?php include_partial('dr/actions', array('etape' => 2, 'help_popup_action' => $help_popup_action)) ?>


<form id="principal" action="<?php echo url_for('dr_repartition_lieu', $dr) ?>" method="post">
    <?php echo $form->renderHiddenFields(); ?>
    <?php echo $form->renderGlobalErrors(); ?>

    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#exploitation_acheteurs">Répartition de la récolte</a></li>
    </ul>
    <div id="application_dr" class="clearfix">
        <?php if($sf_user->hasFlash('erreur_global')): ?>
            <ul style="margin-bottom: 40px;" class="error_list"><li><?php echo $sf_user->getFlash('erreur_global') ?></li></ul>
        <?php endif; ?>
        <?php foreach($form['appellations'] as $hash => $formAppellation): ?>
            <?php include_partial('dr/formLieu', array('appellation' => $dr->get($hash), 'form' => $formAppellation, 'dr' => $dr)) ?>
        <?php endforeach; ?>
    </div>

    <div id="popup_msg_erreur" class="popup_ajout" title="Erreur !">
        <p><?php include_partial('global/message', array('id'=>'err_exploitation_lieudits_popup_no_required')); ?></p>

    </div>

    <?php include_partial('dr/boutons', array('display' => array('precedent','suivant'), 'dr' => $dr)) ?>
</form>
<!-- fin #principal -->
