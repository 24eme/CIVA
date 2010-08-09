<?php foreach($details as $key => $detail): ?>
    <?php if (in_array($detail_action_mode, array('add', 'update')) && $key == $detail_key): ?>
        <?php include_partial('detailForm', array('detail' => $detail, 'key' => $key, 'onglets' => $onglets, 'form' => $form, 'is_new' => ($detail_action_mode == 'add'))) ?>
    <?php else: ?>
        <?php include_partial('detail', array('detail' => $detail, 'key' => $key, 'onglets' => $onglets)) ?>
    <?php endif; ?>
<?php endforeach; ?>
