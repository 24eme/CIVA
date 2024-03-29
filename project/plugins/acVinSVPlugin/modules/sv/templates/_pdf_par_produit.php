<?php use_helper('TemplatingPDF');
 use_helper('Float');
 use_helper('Compte');
 use_helper("Date"); ?>

<style>
  <?php echo pdfStyle(); ?>
  .tableau td, .tableau th, .tableau table {border: 1px solid black;}
  .tableau th.th { font-weight: bold; }
  table {
    padding-left: 0px;
    padding-right: 5px;
  }
  pre {display: inline;}
  .h3Alt {
      background-color: #f3c3d3; color: #c75268; font-weight: bold;
  }
  .h3 {
      background-color: #c75268; color: white; font-weight: bold;
  }
</style>

<span class="h3Alt">&nbsp;Entreprise&nbsp;</span><br/>
<table class="tableau">
  <tr><td>
    <table border="0" >
      <tr>
        <td>&nbsp;Nom : <i><?php echo $document->declarant->raison_sociale ?></i></td>
        <td>&nbsp;N° CVI : <i><?php echo $document->declarant->cvi ?></i></td>
      </tr>
      <tr>
        <td>&nbsp;Adresse : <i><?php echo str_replace('−', '-', $document->declarant->adresse); ?></i></td>
        <td>&nbsp;Commune : <i><?php echo $document->declarant->code_postal; ?> <?php echo $document->declarant->commune; ?></i></td>
      </tr>
    </table>
  </td></tr>
</table>

<br />

<div>
<span class="h3">&nbsp;Récapitulatif par produit&nbsp;</span>
</div>

<?php if (count($produits)): ?>
  <table border="1" class="table" cellspacing=0 cellpadding=0 style="text-align: right;">
    <tr>
      <th class="th" style="text-align: left; width: 531px">&nbsp;Produit</th>
      <th class="th" style="text-align: center; width: 83px">&nbsp;Superficie déclarée</th>
      <?php if ($document->getType() === SVClient::TYPE_SV11): ?>
        <th class="th" style="text-align: center; width: 83px">&nbsp;Volume récolté</th>
        <th class="th" style="text-align: center; width: 83px">&nbsp;Volume revendiqué</th>
        <th class="th" style="text-align: center; width: 83px">&nbsp;Volume à détruire</th>
        <th class="th" style="text-align: center; width: 83px">&nbsp;VCI</th>
      <?php else: ?>
        <th class="th" style="text-align: center; width: 83px">&nbsp;Quantité récoltée</th>
        <th class="th" style="text-align: center; width: 83px">&nbsp;Volume revendiqué</th>
        <th class="th" style="text-align: center; width: 83px">&nbsp;Volume de moûts</th>
        <th class="th" style="text-align: center; width: 83px">&nbsp;Volume de moûts revendique</th>
      <?php endif ?>
    </tr>
    <?php foreach($produits as $hash => $produit): ?>
    <?php if ($hash !== ExportSVPdf::KEY_AUTRES_PRODUITS): ?>
      <tr>
        <td class="td" style="text-align: left;"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo $produit->libelle; ?></td>
        <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo echoLongFloatFr($produit->superficie_totale / 100); ?>&nbsp;<small>ha</small></td>
        <?php if ($document->getType() === SVClient::TYPE_SV11): ?>
          <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($produit->volume_recolte) ?>&nbsp;<small>hl</small></td>
          <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($produit->volume_revendique) ?>&nbsp;<small>hl</small></td>
          <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($produit->volume_detruit) ?>&nbsp;<small>hl</small></td>
          <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($produit->vci) ?>&nbsp;<small>hl</small></td>
        <?php else: ?>
          <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo $produit->quantite_recolte ?>&nbsp;<small>kg</small></td>
          <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($produit->volume_revendique) ?>&nbsp;<small>hl</small></td>
          <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr(isset($produit->volume_mouts) ? $produit->volume_mouts : 0.00) ?>&nbsp;<small>hl</small></td>
          <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr(isset($produit->volume_mouts_revendique) ? $produit->volume_mouts_revendique : 0.00) ?>&nbsp;<small>hl</small></td>
        <?php endif ?>
      </tr>
    <?php endif; ?>
    <?php endforeach; ?>
    <?php if (isset($produits[ExportSVPdf::KEY_AUTRES_PRODUITS])): ?>
    <tr>
      <th class="th" style="text-align: left;"><?php echo pdfTdLargeStart(); ?>&nbsp;Total</th>
      <th class="th"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo echoLongFloatFr($document->getSum()['superficie'] / 100); ?>&nbsp;<small>ha</small></th>
      <?php if ($document->getType() === SVClient::TYPE_SV11): ?>
        <th class="th"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($document->getSum()['recolte']) ?>&nbsp;<small>hl</small></th>
        <th class="th"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($document->getSum()['revendique']) ?>&nbsp;<small>hl</small></th>
        <th class="th"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($document->getSum()['volume_detruit']) ?>&nbsp;<small>hl</small></th>
        <th class="th"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($document->getSum()['vci']) ?>&nbsp;<small>hl</small></th>
      <?php else: ?>
        <th class="th"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo $document->getSum()['recolte'] ?>&nbsp;<small>kg</small></th>
        <th class="th"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($document->getSum()['revendique']) ?>&nbsp;<small>hl</small></th>
        <th class="th"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr(isset($document->getSum()['mouts']) ? $document->getSum()['mouts'] : 0.00) ?>&nbsp;<small>hl</small></th>
        <th class="th"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr(isset($document->getSum()['revendique_mouts']) ? $document->getSum()['revendique_mouts'] : 0.00) ?>&nbsp;<small>hl</small></th>
      <?php endif ?>
    </tr>
    <?php endif; ?>
  </table>

  <?php if (isset($produits[ExportSVPdf::KEY_AUTRES_PRODUITS])): ?>
  <br/>
  <div><span class="h3">Récapitulatif&nbsp;<?php echo ExportSVPdf::KEY_AUTRES_PRODUITS ?>&nbsp;</span></div>

  <table border="1" class="table" cellspacing=0 cellpadding=0 style="text-align: right;">
    <tr>
      <th class="th" style="text-align: left; width: 531px">&nbsp;Produit</th>
      <th class="th" style="text-align: center; width: 83px">&nbsp;Volume total</th>
    </tr>
    <tbody>
      <?php foreach ($produits[ExportSVPdf::KEY_AUTRES_PRODUITS] as $produit => $autre): ?>
      <tr>
        <td class="td" style="text-align: left;"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo $produit; ?></td>
        <td class="td"><?php echo pdfTdLargeStart(); ?>&nbsp;<?php echo sprintFloatFr($autre); ?>&nbsp;<small>hl</small></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <?php endif ?>
<?php else: ?>
  <br />
  <em>Aucun produit déclaré</em>
<?php endif; ?>
