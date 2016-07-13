<?php use_helper('Date'); ?>
<?php use_helper('DRM'); ?>
<?php use_helper('Orthographe'); ?>
<div id="drm_nouvelle_<?php echo $periode . '_' . $identifiant; ?>"  class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form action="<?php echo url_for('drm_choix_creation', array('identifiant' => $identifiant, 'periode' => $periode)); ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
            <div class="modal-content">
                <div class="modal-header"><h2>Création de la DRM <?php echo getFrPeriodeElision($periode); ?></h2></div>
                <div class="modal-body">
                    <?php echo $drmCreationForm->renderHiddenFields(); ?>
                    <?php echo $drmCreationForm->renderGlobalErrors(); ?>
                    <div class="form-group bloc_condition" data-condition-cible="#bloc_fichier_<?php echo $periode . '_' . $identifiant; ?>">
                            <?php echo $drmCreationForm['type_creation']->renderError(); ?>
                            <?php echo $drmCreationForm['type_creation']->renderLabel("Type de création", array('class' => "control-label col-xs-3")) ?>
                            <div class="col-xs-9">
                            <?php echo $drmCreationForm['type_creation']->render(); ?>
                            </div>
                    </div>
                     <div id="bloc_fichier_<?php echo $periode . '_' . $identifiant; ?>" data-condition-value="CREATION_EDI" class="bloc_conditionner form-group">
                        <div class="col-xs-offset-3">
                            <?php echo $drmCreationForm['file']->renderError(); ?>
                            <?php echo $drmCreationForm['file']->renderLabel(); ?>
                            <?php echo $drmCreationForm['file']->render(); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
                    <button id="drm_nouvelle_popup_confirm" type="submit" class="btn btn-success pull-right"><span>Commencer la DRM</span></button>
                </div>
            </div>
        </form>
    </div>
</div>
