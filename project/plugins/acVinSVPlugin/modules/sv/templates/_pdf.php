<?php use_helper('TemplatingPDF');
 use_helper('Float');
 use_helper('Compte');
 use_helper("Date"); ?>

<style>
  <?php echo pdfStyle(); ?>
  .tableau td, .tableau th, .tableau table {border: 1px solid black;}
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
        <td>&nbsp;N° Adhérent : <i><?php echo preg_replace('/..$/', '', $document->identifiant); ?></i></td>
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
<span class="h3">&nbsp;Déclaration de production&nbsp;</span>
</div>

<?php if (count($produits)): ?>
  <table border="1" class="table" cellspacing=0 cellpadding=0 style="text-align: right;">
    <tr>
      <th class="th" style="text-align: left; width: 503px">&nbsp;Produit</th>
      <th class="th" style="text-align: center; width: 107px">&nbsp;Superficie déclarée</th>
      <?php if ($document->getType() === SVClient::TYPE_SV11): ?>
        <th class="th" style="text-align: center; width: 84px">&nbsp;Volume récolté</th>
        <th class="th" style="text-align: center; width: 84px">&nbsp;Volume revendiqué</th>
        <th class="th" style="text-align: center; width: 84px">&nbsp;Volume à détruire</th>
        <th class="th" style="text-align: center; width: 84px">&nbsp;VCI</th>
      <?php else: ?>
        <th class="th" style="text-align: center; width: 112px">&nbsp;Quantité récoltée</th>
        <th class="th" style="text-align: center; width: 112px">&nbsp;Volume revendiqué</th>
        <th class="th" style="text-align: center; width: 112px">&nbsp;Volume de moûts</th>
      <?php endif ?>
    </tr>
    <?php foreach($produits as $produit): ?>
      <tr>
        <td class="td" style="text-align: left;">&nbsp;<?php echo $produit->libelle; ?></td>
        <td class="td">&nbsp;<?php echo $produit->superficie_recolte; ?>&nbsp;<small>ares</small></td>
        <?php if ($document->getType() === SVClient::TYPE_SV11): ?>
          <td class="td">&nbsp;<?php echo sprintFloatFr($produit->volume_recolte) ?>&nbsp;<small>hl</small></td>
          <td class="td">&nbsp;<?php echo sprintFloatFr($produit->volume_revendique) ?>&nbsp;<small>hl</small></td>
          <td class="td">&nbsp;<?php echo sprintFloatFr($produit->usages_industriels) ?>&nbsp;<small>hl</small></td>
          <td class="td">&nbsp;<?php echo sprintFloatFr($produit->vci) ?>&nbsp;<small>hl</small></td>
        <?php else: ?>
          <td class="td">&nbsp;<?php echo sprintFloatFr($produit->quantite_recolte) ?>&nbsp;<small>kg</small></td>
          <td class="td">&nbsp;<?php echo sprintFloatFr($produit->volume_revendique) ?>&nbsp;<small>hl</small></td>
          <td class="td">&nbsp;<?php echo sprintFloatFr(0.00) ?>&nbsp;<small>hl</small></td>
        <?php endif ?>
      </tr>
    <?php endforeach; ?>
  </table>
<?php else: ?>
  <br />
  <em>Aucun produit déclaré</em>
<?php endif; ?>
