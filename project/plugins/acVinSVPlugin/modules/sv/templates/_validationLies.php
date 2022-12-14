<div class="row">
  <div class="col-xs-6">
    <h3 style="margin-top: 0;">Autres produits</h3>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th class="col-xs-9">Produits</th>
          <th class="col-xs-3 text-center">Volume total</th>
        </tr>
      </thead>
      <tbody>
      <tr>
        <td>Lies et bourbes</td>
        <td class="text-right"><?php echoFloat($sv->lies ? $sv->lies : 0) ?> <small class="text-muted">hl</small></td>
      </tr>
      <tr>
        <td>Rebêches</td>
        <td class="text-right"><?php echoFloat($sv->rebeches ? $sv->rebeches : 0) ?> <small class="text-muted">hl</small></td>
      </tr>
      </tbody>
    </table>
  </div>
</div>

