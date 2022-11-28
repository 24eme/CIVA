<?php include_partial('sv/step', ['object' => $sv, 'etapes' => SVEtapes::getInstance(), 'step' => SVEtapes::ETAPE_LIEU_STOCKAGE]) ?>

<h3>Lieu de stockage</h3>

<div class="alert alert-warning" role="alert">
  <strong><i class="glyphicon glyphicon-warning-sign"></i> Attention</strong>
  <br/>
  Si vous avez plusieurs lieux de stockage, merci de contacter le CIVA afin de leur communiquer votre répartition.
</div>

<div class="row">
  <div class="col-xs-6">
    <a href="<?php echo url_for('sv_autres', $sv) ?>" class="btn btn-default">
      <span class="glyphicon glyphicon-chevron-left"></span> Étape précédente
    </a>
  </div>
  <div class="col-xs-6 text-right">
    <a href="<?php echo url_for('sv_validation', $sv) ?>" class="btn btn-success">
      <span class="glyphicon glyphicon-chevron-right"></span> Étape suivante
    </a>
  </div>
</div>
