<?php include_partial('global/etapes', array('etape' => 2)) ?>
<?php include_partial('global/actions', array('etape' => 2, 'help_popup_action'=>$help_popup_action)) ?>


<form id="principal" action="<?php echo url_for('@exploitation_lieu') ?>" method="post">
    <ul id="onglets_majeurs" class="clearfix">
        <li class="ui-tabs-selected"><a href="#exploitation_acheteurs">Répartition de la récolte</a></li>
    </ul>

    <div id="application_dr" class="clearfix">
        <?php foreach($appellations as $key => $appellation): ?>
            <?php include_partial('acheteur/formLieu', array('appellation' => $appellation, 'form' => $forms[$key])) ?>
        <?php endforeach; ?>
    </div>

    <div id="popup_msg_erreur" class="popup_ajout" title="Erreur !">
        <p><?php include_partial('global/message', array('id'=>'err_exploitation_lieudits_popup_no_required')); ?></p>
        
    </div>

    <?php include_partial('global/boutons', array('display' => array('precedent','suivant'))) ?>

</form>
<!-- fin #principal -->
