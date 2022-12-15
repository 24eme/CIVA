<h3>Récapitulatif</h3>

<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active"><a href="#recap-produit" aria-controls="recap-produit" role="tab" data-toggle="tab">Récapitulatif par produit</a></li>
  <li role="presentation"><a href="#recap-stockage" aria-controls="recap-stockage" role="tab" data-toggle="tab">Répartition par lieux de stockage</a></li>
</ul>

<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="recap-produit">
    <?php if ($sv->getType() === SVClient::TYPE_SV11): ?>
      <?php include_partial('sv/validationSV11', ['sv' => $sv]); ?>
    <?php else: ?>
      <?php include_partial('sv/validationSV12', ['sv' => $sv]); ?>
    <?php endif ?>
  </div>

  <div role="tabpanel" class="tab-pane" id="recap-stockage">
    <?php include_partial('sv/validationStockage', ['sv' => $sv]); ?>
  </div>
</div>

<?php include_partial('sv/validationLies', ['sv' => $sv]); ?>

