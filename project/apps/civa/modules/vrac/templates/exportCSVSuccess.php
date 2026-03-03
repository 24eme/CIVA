<?php use_helper('Float'); ?>
<?php echo VracCsvExport::header() ?>
<?php foreach($vracs as $item): ?>
<?php $vrac = acCouchdbManager::getClient()->find($item->id) ?>
<?php echo VracCsvExport::contrat($vrac) ?>
<?php endforeach; ?>
