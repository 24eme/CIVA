<?php if ($sv->exist('_attachments')): ?>
<div class="row">
  <div class="col-xs-6">
    <h3>Fichier</h3>
    <p>
    Télécharger le <a href="<?php echo url_for('sv_csv', ['id' => $sv->_id]); ?>">fichier versé</a> lors de la création.
    </p>
  </div>
</div>
<?php endif ?>
